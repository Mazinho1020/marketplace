<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaUsuarioPermissao extends Model
{
    use SoftDeletes;

    protected $table = 'empresa_usuario_permissoes';

    protected $fillable = [
        'usuario_id',
        'permissao_id',
        'empresa_id',
        'is_concedida',
        'atribuido_por',
        'data_atribuicao',
        'data_expiracao',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'is_concedida' => 'boolean',
        'data_atribuicao' => 'datetime',
        'data_expiracao' => 'datetime',
        'sync_data' => 'datetime'
    ];

    // Relacionamentos
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\EmpresaUsuario::class, 'usuario_id');
    }

    public function permissao(): BelongsTo
    {
        return $this->belongsTo(EmpresaPermissao::class, 'permissao_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class, 'empresa_id');
    }

    public function atribuidoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User\EmpresaUsuario::class, 'atribuido_por');
    }

    // Scopes
    public function scopeConcedidas($query)
    {
        return $query->where('is_concedida', true);
    }

    public function scopeNegadas($query)
    {
        return $query->where('is_concedida', false);
    }

    public function scopeValidas($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('data_expiracao')
                ->orWhere('data_expiracao', '>', now());
        });
    }

    public function scopeExpiradas($query)
    {
        return $query->whereNotNull('data_expiracao')
            ->where('data_expiracao', '<=', now());
    }

    // Métodos
    public function isExpired(): bool
    {
        return $this->data_expiracao && $this->data_expiracao->isPast();
    }

    public function isValid(): bool
    {
        return $this->is_concedida && !$this->isExpired();
    }
}
