<?php

namespace App\Traits;

use App\Services\Permission\PermissionService;
use App\Models\Permission\EmpresaPermissao;
use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Verifica se o usuário tem uma permissão
     */
    public function hasPermission(string $permissionCode): bool
    {
        return app(PermissionService::class)->hasPermission($this, $permissionCode);
    }

    /**
     * Verifica se o usuário tem qualquer uma das permissões
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se o usuário tem todas as permissões
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Obtém todas as permissões do usuário
     */
    public function getAllPermissions(): Collection
    {
        return app(PermissionService::class)->getUserPermissions($this);
    }

    /**
     * Obtém todos os papéis do usuário
     */
    public function getRoles(): Collection
    {
        return app(PermissionService::class)->getUserRoles($this);
    }

    /**
     * Verifica se tem papel
     */
    public function hasRole(string $roleCode): bool
    {
        return $this->getRoles()->contains('codigo', $roleCode);
    }

    /**
     * Concede permissão
     */
    public function givePermission(string $permissionCode): bool
    {
        return app(PermissionService::class)->grantPermission($this, $permissionCode);
    }

    /**
     * Revoga permissão
     */
    public function revokePermission(string $permissionCode): bool
    {
        return app(PermissionService::class)->revokePermission($this, $permissionCode);
    }

    /**
     * Atribui papel
     */
    public function assignRole(string $roleCode): bool
    {
        return app(PermissionService::class)->assignRole($this, $roleCode);
    }

    /**
     * Remove papel
     */
    public function removeRole(string $roleCode): bool
    {
        return app(PermissionService::class)->removeRole($this, $roleCode);
    }
}
