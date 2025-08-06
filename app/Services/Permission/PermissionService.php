<?php

namespace App\Services\Permission;

use App\Models\Permission\EmpresaPermissao;
use App\Models\Permission\EmpresaPapel;
use App\Models\Permission\EmpresaUsuarioPermissao;
use App\Models\Permission\EmpresaUsuarioPapel;
use App\Models\Permission\EmpresaPapelPermissao;
use App\Models\Permission\EmpresaLogPermissao;
use App\Models\User\EmpresaUsuario;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PermissionService
{
    /**
     * Verifica se um usuário tem uma permissão específica
     */
    public function hasPermission(EmpresaUsuario $user, string $permissionCode): bool
    {
        $cacheKey = "user_permission_{$user->id}_{$permissionCode}";

        return Cache::remember($cacheKey, 1800, function () use ($user, $permissionCode) {
            // 1. Verificar permissão direta primeiro
            $directPermission = $this->getDirectPermission($user, $permissionCode);
            if ($directPermission) {
                return $directPermission->is_concedida && $directPermission->isValid();
            }

            // 2. Verificar através de papéis
            return $this->hasPermissionThroughRoles($user, $permissionCode);
        });
    }

    /**
     * Verifica permissão através de papéis
     */
    protected function hasPermissionThroughRoles(EmpresaUsuario $user, string $permissionCode): bool
    {
        $userRoles = $this->getUserRoles($user);

        foreach ($userRoles as $role) {
            $hasPermission = EmpresaPapelPermissao::where('papel_id', $role->id)
                ->whereHas('permissao', function ($q) use ($permissionCode) {
                    $q->where('codigo', $permissionCode);
                })
                ->exists();

            if ($hasPermission) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtém permissão direta do usuário
     */
    protected function getDirectPermission(EmpresaUsuario $user, string $permissionCode): ?EmpresaUsuarioPermissao
    {
        $permissao = EmpresaPermissao::getByCode($permissionCode, $user->empresa_id);

        if (!$permissao) {
            return null;
        }

        return EmpresaUsuarioPermissao::where('usuario_id', $user->id)
            ->where('permissao_id', $permissao->id)
            ->where('empresa_id', $user->empresa_id)
            ->first();
    }

    /**
     * Obtém todos os papéis do usuário
     */
    public function getUserRoles(EmpresaUsuario $user): Collection
    {
        $cacheKey = "user_roles_{$user->id}";

        return Cache::remember($cacheKey, 1800, function () use ($user) {
            return EmpresaPapel::whereHas('usuarios', function ($q) use ($user) {
                $q->where('usuario_id', $user->id)
                    ->where('empresa_id', $user->empresa_id);
            })->get();
        });
    }

    /**
     * Obtém todas as permissões do usuário (diretas + papéis)
     */
    public function getUserPermissions(EmpresaUsuario $user): Collection
    {
        $cacheKey = "user_all_permissions_{$user->id}";

        return Cache::remember($cacheKey, 1800, function () use ($user) {
            // Permissões diretas
            $directPermissions = EmpresaPermissao::whereHas('usuarioPermissoes', function ($q) use ($user) {
                $q->where('usuario_id', $user->id)
                    ->where('empresa_id', $user->empresa_id)
                    ->where('is_concedida', true)
                    ->validas();
            })->get();

            // Permissões via papéis
            $rolePermissions = EmpresaPermissao::whereHas('papelPermissoes.papel.usuarios', function ($q) use ($user) {
                $q->where('usuario_id', $user->id)
                    ->where('empresa_id', $user->empresa_id);
            })->get();

            $permissions = $directPermissions->merge($rolePermissions);
            return $permissions->unique('id');
        });
    }

    /**
     * Concede permissão direta a um usuário
     */
    public function grantPermission(
        EmpresaUsuario $user,
        string $permissionCode,
        ?EmpresaUsuario $grantedBy = null,
        ?\DateTime $expiresAt = null
    ): bool {
        DB::beginTransaction();

        try {
            $permissao = EmpresaPermissao::getByCode($permissionCode, $user->empresa_id);
            if (!$permissao) {
                throw new \Exception("Permissão '{$permissionCode}' não encontrada");
            }

            EmpresaUsuarioPermissao::updateOrCreate([
                'usuario_id' => $user->id,
                'permissao_id' => $permissao->id,
                'empresa_id' => $user->empresa_id
            ], [
                'is_concedida' => true,
                'atribuido_por' => $grantedBy?->id,
                'data_atribuicao' => now(),
                'data_expiracao' => $expiresAt,
                'sync_status' => 'pendente'
            ]);

            // Log da mudança
            $this->logPermissionChange(
                $user,
                'conceder_permissao',
                'permissao',
                $permissao->id,
                $grantedBy
            );

            // Limpar cache
            $this->clearUserCache($user);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Revoga permissão direta de um usuário
     */
    public function revokePermission(
        EmpresaUsuario $user,
        string $permissionCode,
        ?EmpresaUsuario $revokedBy = null
    ): bool {
        DB::beginTransaction();

        try {
            $permissao = EmpresaPermissao::getByCode($permissionCode, $user->empresa_id);
            if (!$permissao) {
                return false;
            }

            $deleted = EmpresaUsuarioPermissao::where('usuario_id', $user->id)
                ->where('permissao_id', $permissao->id)
                ->where('empresa_id', $user->empresa_id)
                ->delete();

            if ($deleted > 0) {
                // Log da mudança
                $this->logPermissionChange(
                    $user,
                    'revogar_permissao',
                    'permissao',
                    $permissao->id,
                    $revokedBy
                );

                // Limpar cache
                $this->clearUserCache($user);
            }

            DB::commit();
            return $deleted > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Atribui papel a um usuário
     */
    public function assignRole(
        EmpresaUsuario $user,
        string $roleCode,
        ?EmpresaUsuario $assignedBy = null
    ): bool {
        DB::beginTransaction();

        try {
            $papel = EmpresaPapel::where('codigo', $roleCode)
                ->where('empresa_id', $user->empresa_id)
                ->first();

            if (!$papel) {
                throw new \Exception("Papel '{$roleCode}' não encontrado");
            }

            $created = EmpresaUsuarioPapel::updateOrCreate([
                'usuario_id' => $user->id,
                'papel_id' => $papel->id,
                'empresa_id' => $user->empresa_id
            ], [
                'atribuido_por' => $assignedBy?->id,
                'data_atribuicao' => now(),
                'sync_status' => 'pendente'
            ]);

            // Log da mudança
            $this->logRoleChange(
                $user,
                'conceder_papel',
                'papel',
                $papel->id,
                $assignedBy
            );

            // Limpar cache
            $this->clearUserCache($user);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove papel de um usuário
     */
    public function removeRole(
        EmpresaUsuario $user,
        string $roleCode,
        ?EmpresaUsuario $removedBy = null
    ): bool {
        DB::beginTransaction();

        try {
            $papel = EmpresaPapel::where('codigo', $roleCode)
                ->where('empresa_id', $user->empresa_id)
                ->first();

            if (!$papel) {
                return false;
            }

            $deleted = EmpresaUsuarioPapel::where('usuario_id', $user->id)
                ->where('papel_id', $papel->id)
                ->where('empresa_id', $user->empresa_id)
                ->delete();

            if ($deleted > 0) {
                // Log da mudança
                $this->logRoleChange(
                    $user,
                    'revogar_papel',
                    'papel',
                    $papel->id,
                    $removedBy
                );

                // Limpar cache
                $this->clearUserCache($user);
            }

            DB::commit();
            return $deleted > 0;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Limpa cache do usuário
     */
    protected function clearUserCache(EmpresaUsuario $user): void
    {
        $patterns = [
            "user_permission_{$user->id}_*",
            "user_roles_{$user->id}",
            "user_all_permissions_{$user->id}"
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Log de mudanças de permissão
     */
    protected function logPermissionChange(
        EmpresaUsuario $user,
        string $acao,
        string $tipoAlvo,
        int $alvoId,
        ?EmpresaUsuario $author = null
    ): void {
        EmpresaLogPermissao::create([
            'usuario_id' => $user->id,
            'autor_id' => $author?->id ?? $user->id,
            'empresa_id' => $user->empresa_id,
            'acao' => $acao,
            'alvo_id' => $alvoId,
            'tipo_alvo' => $tipoAlvo,
            'detalhes' => json_encode([
                'timestamp' => now()->toISOString(),
                'user_agent' => request()->userAgent(),
                'action_details' => "Ação '{$acao}' executada"
            ]),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'sync_status' => 'pendente'
        ]);
    }

    /**
     * Log de mudanças de papel
     */
    protected function logRoleChange(
        EmpresaUsuario $user,
        string $acao,
        string $tipoAlvo,
        int $alvoId,
        ?EmpresaUsuario $author = null
    ): void {
        EmpresaLogPermissao::create([
            'usuario_id' => $user->id,
            'autor_id' => $author?->id ?? $user->id,
            'empresa_id' => $user->empresa_id,
            'acao' => $acao,
            'alvo_id' => $alvoId,
            'tipo_alvo' => $tipoAlvo,
            'detalhes' => json_encode([
                'timestamp' => now()->toISOString(),
                'user_agent' => request()->userAgent(),
                'action_details' => "Ação '{$acao}' executada"
            ]),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'sync_status' => 'pendente'
        ]);
    }
}
