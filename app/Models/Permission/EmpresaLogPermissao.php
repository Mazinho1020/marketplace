<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaLogPermissao extends Model
{
    protected $table = 'empresa_logs_permissoes';

    protected $fillable = [
        'usuario_id',
        'autor_id',
        'empresa_id',
        'acao',
        'alvo_id',
        'tipo_alvo',
        'detalhes',
        'ip',
        'user_agent',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'detalhes' => 'array',
        'sync_data' => 'datetime',
        'created_at' => 'datetime'
    ];

    // Relacionamentos
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\EmpresaUsuario::class, 'usuario_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\EmpresaUsuario::class, 'autor_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    // Scopes
    public function scopeForEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('usuario_id', $userId);
    }

    public function scopeByAction($query, $acao)
    {
        return $query->where('acao', $acao);
    }
}
