<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransacaoPontos extends Model
{
    protected $table = 'transacoes_pontos';

    protected $fillable = [
        'cartao_fidelidade_id',
        'programa_fidelidade_id',
        'tipo',
        'pontos',
        'valor_referencia',
        'descricao',
        'metadata',
        'pdv_sale_id',
        'processed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'pontos' => 'integer',
        'valor_referencia' => 'decimal:2'
    ];

    /**
     * Tipos de transação disponíveis
     */
    const TIPOS = [
        'acumulo' => 'Acúmulo de Pontos',
        'resgate' => 'Resgate de Pontos',
        'bonus' => 'Bônus',
        'ajuste' => 'Ajuste',
        'expiracao' => 'Expiração'
    ];

    /**
     * Relacionamento com CartaoFidelidade
     */
    public function cartao(): BelongsTo
    {
        return $this->belongsTo(CartaoFidelidade::class, 'cartao_fidelidade_id');
    }

    /**
     * Relacionamento com ProgramaFidelidade
     */
    public function programa(): BelongsTo
    {
        return $this->belongsTo(ProgramaFidelidade::class, 'programa_fidelidade_id');
    }

    /**
     * Relacionamento com PDV Sale (se aplicável)
     */
    public function venda()
    {
        return $this->belongsTo(\App\Models\PDV\Sale::class, 'pdv_sale_id');
    }

    /**
     * Scope para transações de acúmulo
     */
    public function scopeAcumulo($query)
    {
        return $query->where('tipo', 'acumulo');
    }

    /**
     * Scope para transações de resgate
     */
    public function scopeResgate($query)
    {
        return $query->where('tipo', 'resgate');
    }

    /**
     * Scope para transações por período
     */
    public function scopePeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('created_at', [$dataInicio, $dataFim]);
    }

    /**
     * Marcar transação como processada
     */
    public function marcarProcessada()
    {
        $this->update(['processed_at' => now()]);
    }

    /**
     * Verificar se a transação foi processada
     */
    public function foiProcessada(): bool
    {
        return !is_null($this->processed_at);
    }

    /**
     * Obter descrição do tipo
     */
    public function getTipoDescricaoAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
