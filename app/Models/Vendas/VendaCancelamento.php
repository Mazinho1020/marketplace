<?php

namespace App\Models\Vendas;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model para Cancelamentos de Vendas
 */
class VendaCancelamento extends BaseModel
{
    protected $table = 'venda_cancelamentos';

    // Tipos de cancelamento
    const TIPO_TOTAL = 'total';
    const TIPO_PARCIAL = 'parcial';

    // Categorias de motivo
    const MOTIVO_CLIENTE_DESISTIU = 'cliente_desistiu';
    const MOTIVO_PRODUTO_INDISPONIVEL = 'produto_indisponivel';
    const MOTIVO_ERRO_PRECO = 'erro_preco';
    const MOTIVO_PROBLEMA_PAGAMENTO = 'problema_pagamento';
    const MOTIVO_OUTROS = 'outros';

    protected $fillable = [
        'empresa_id', 'lancamento_id', 'tipo_cancelamento', 'motivo_categoria',
        'motivo_detalhado', 'valor_cancelado', 'valor_reembolso', 'usuario_id',
        'aprovado_por_id', 'data_cancelamento', 'data_reembolso', 'observacoes',
    ];

    protected $casts = [
        'valor_cancelado' => 'decimal:2',
        'valor_reembolso' => 'decimal:2',
        'data_cancelamento' => 'datetime',
        'data_reembolso' => 'datetime',
    ];

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

    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'aprovado_por_id');
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

    public function scopePendentesAprovacao(Builder $query): Builder
    {
        return $query->whereNull('aprovado_por_id');
    }

    /**
     * Métodos de negócio
     */
    public function aprovar(int $usuarioId, float $valorReembolso = null): bool
    {
        if ($this->isAprovado()) {
            return false;
        }

        $this->aprovado_por_id = $usuarioId;
        
        if ($valorReembolso !== null) {
            $this->valor_reembolso = $valorReembolso;
        }

        return $this->save();
    }

    public function isAprovado(): bool
    {
        return $this->aprovado_por_id !== null;
    }

    public function isCancelamentoTotal(): bool
    {
        return $this->tipo_cancelamento === self::TIPO_TOTAL;
    }

    public function temReembolso(): bool
    {
        return $this->valor_reembolso > 0;
    }
}