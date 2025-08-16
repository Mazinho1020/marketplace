<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Model de Movimentações de Lançamentos
 * 
 * Gerencia pagamentos, recebimentos e estornos dos lançamentos
 * 
 * @property int $id
 * @property int $lancamento_id
 * @property string $tipo
 * @property float $valor
 * @property Carbon $data_movimentacao
 * @property string|null $observacoes
 */
class LancamentoMovimentacao extends Model
{
    use HasFactory;

    protected $table = 'lancamento_movimentacoes';

    public $timestamps = false; // Apenas created_at

    protected $fillable = [
        'lancamento_id',
        'tipo',
        'valor',
        'data_movimentacao',
        'forma_pagamento_id',
        'conta_bancaria_id',
        'numero_documento',
        'observacoes',
        'metadados',
        'usuario_id',
        'empresa_id',
    ];

    protected $casts = [
        'data_movimentacao' => 'datetime',
        'valor' => 'decimal:4',
        'metadados' => 'json',
        'created_at' => 'datetime',
    ];

    /**
     * Tipos de movimentação
     */
    const TIPO_PAGAMENTO = 'pagamento';
    const TIPO_RECEBIMENTO = 'recebimento';
    const TIPO_ESTORNO = 'estorno';

    /**
     * Relacionamentos
     */
    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(Lancamento::class, 'lancamento_id');
    }

    /**
     * Scopes
     */
    public function scopePorLancamento($query, $lancamentoId)
    {
        return $query->where('lancamento_id', $lancamentoId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePagamentos($query)
    {
        return $query->where('tipo', self::TIPO_PAGAMENTO);
    }

    public function scopeRecebimentos($query)
    {
        return $query->where('tipo', self::TIPO_RECEBIMENTO);
    }

    public function scopeEstornos($query)
    {
        return $query->where('tipo', self::TIPO_ESTORNO);
    }

    public function scopeEntreDatas($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_movimentacao', [$dataInicio, $dataFim]);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Métodos de utilidade
     */
    public function isPagamento(): bool
    {
        return $this->tipo === self::TIPO_PAGAMENTO;
    }

    public function isRecebimento(): bool
    {
        return $this->tipo === self::TIPO_RECEBIMENTO;
    }

    public function isEstorno(): bool
    {
        return $this->tipo === self::TIPO_ESTORNO;
    }

    /**
     * Formatters para exibição
     */
    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getTipoFormatadoAttribute(): string
    {
        $tipos = [
            self::TIPO_PAGAMENTO => 'Pagamento',
            self::TIPO_RECEBIMENTO => 'Recebimento',
            self::TIPO_ESTORNO => 'Estorno',
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }

    public function getDataMovimentacaoFormatadaAttribute(): string
    {
        return $this->data_movimentacao->format('d/m/Y H:i');
    }

    /**
     * Verificações
     */
    public function podeSerEstornado(): bool
    {
        // Não pode estornar um estorno
        if ($this->isEstorno()) {
            return false;
        }

        // Verificar se já foi estornado
        $jaEstornado = self::where('lancamento_id', $this->lancamento_id)
                          ->where('tipo', self::TIPO_ESTORNO)
                          ->where('observacoes', 'like', "%#{$this->id}%")
                          ->exists();

        return !$jaEstornado;
    }
}
