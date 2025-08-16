<?php

namespace App\Models\Vendas;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model para Histórico de Status de Vendas
 */
class VendaStatusHistorico extends BaseModel
{
    protected $table = 'venda_status_historico';

    protected $fillable = [
        'empresa_id', 'lancamento_id', 'status_anterior', 'status_novo',
        'usuario_id', 'motivo', 'observacoes', 'dados_contexto',
        'ip_address', 'user_agent', 'data_mudanca',
    ];

    protected $casts = [
        'dados_contexto' => 'array',
        'data_mudanca' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relacionamentos
     */
    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class, 'lancamento_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Scopes
     */
    public function scopePorVenda(Builder $query, int $vendaId): Builder
    {
        return $query->where('lancamento_id', $vendaId);
    }

    public function scopePorEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeOrdenadoPorData(Builder $query, string $direcao = 'desc'): Builder
    {
        return $query->orderBy('data_mudanca', $direcao);
    }
}