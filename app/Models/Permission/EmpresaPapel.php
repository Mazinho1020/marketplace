<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaPapel extends Model
{
    use SoftDeletes;

    protected $table = 'empresa_papeis';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'nivel_acesso',
        'is_ativo',
        'is_sistema',
        'empresa_id',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'is_ativo' => 'boolean',
        'is_sistema' => 'boolean',
        'nivel_acesso' => 'integer',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function permissoes(): HasMany
    {
        return $this->hasMany(EmpresaPapelPermissao::class, 'papel_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(EmpresaUsuarioPapel::class, 'papel_id');
    }

    // Métodos
    public function hasPermission(string $permissionCode): bool
    {
        return $this->permissoes()
            ->whereHas('permissao', function ($q) use ($permissionCode) {
                $q->where('codigo', $permissionCode);
            })
            ->exists();
    }

    public function grantPermission(string $permissionCode): bool
    {
        $permissao = EmpresaPermissao::getByCode($permissionCode, $this->empresa_id);

        if (!$permissao) {
            return false;
        }

        return $this->permissoes()->updateOrCreate([
            'permissao_id' => $permissao->id
        ], [
            'empresa_id' => $this->empresa_id,
            'sync_status' => 'pendente'
        ]) ? true : false;
    }

    public function revokePermission(string $permissionCode): bool
    {
        $permissao = EmpresaPermissao::getByCode($permissionCode, $this->empresa_id);

        if (!$permissao) {
            return false;
        }

        return $this->permissoes()
            ->where('permissao_id', $permissao->id)
            ->delete() > 0;
    }
}
