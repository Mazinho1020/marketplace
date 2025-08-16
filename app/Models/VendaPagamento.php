<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Pagamentos das Vendas
 * 
 * Conecta vendas com formas de pagamento
 * Suporta múltiplas formas de pagamento por venda
 * 
 * @property int $id
 * @property int $venda_id
 * @property int|null $pagamento_id
 * @property int $forma_pagamento_id
 * @property float $valor_pagamento
 * @property int $parcelas
 * @property string $status_pagamento
 */
class VendaPagamento extends Model
{
    use HasFactory;

    protected $table = 'venda_pagamentos';

    protected $fillable = [
        'venda_id',
        'pagamento_id',
        'forma_pagamento_id',
        'bandeira_id',
        'valor_pagamento',
        'parcelas',
        'valor_parcela',
        'data_pagamento',
        'data_compensacao',
        'status_pagamento',
        'referencia_externa',
        'autorizacao',
        'nsu',
        'taxa_percentual',
        'valor_taxa',
        'valor_liquido',
        'observacoes',
        'metadados',
        'empresa_id',
        'usuario_id'
    ];

    protected $casts = [
        'valor_pagamento' => 'decimal:2',
        'valor_parcela' => 'decimal:2',
        'taxa_percentual' => 'decimal:4',
        'valor_taxa' => 'decimal:2',
        'valor_liquido' => 'decimal:2',
        'data_pagamento' => 'datetime',
        'data_compensacao' => 'datetime',
        'metadados' => 'array',
    ];

    // Status possíveis
    const STATUS_PROCESSANDO = 'processando';
    const STATUS_CONFIRMADO = 'confirmado';
    const STATUS_CANCELADO = 'cancelado';
    const STATUS_ESTORNADO = 'estornado';

    /**
     * Relacionamentos
     */
    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function pagamento(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Financeiro\Pagamento::class, 'pagamento_id');
    }

    public function formaPagamento(): BelongsTo
    {
        return $this->belongsTo(\App\Models\FormaPagamento::class, 'forma_pagamento_id');
    }

    public function bandeira(): BelongsTo
    {
        return $this->belongsTo(\App\Models\FormaPagamentoBandeira::class, 'bandeira_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    /**
     * Scopes
     */
    public function scopePorVenda($query, $vendaId)
    {
        return $query->where('venda_id', $vendaId);
    }

    public function scopeConfirmados($query)
    {
        return $query->where('status_pagamento', self::STATUS_CONFIRMADO);
    }

    public function scopeEstornados($query)
    {
        return $query->where('status_pagamento', self::STATUS_ESTORNADO);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorFormaPagamento($query, $formaPagamentoId)
    {
        return $query->where('forma_pagamento_id', $formaPagamentoId);
    }

    /**
     * Métodos utilitários
     */
    public function isConfirmado(): bool
    {
        return $this->status_pagamento === self::STATUS_CONFIRMADO;
    }

    public function isEstornado(): bool
    {
        return $this->status_pagamento === self::STATUS_ESTORNADO;
    }

    public function isPendente(): bool
    {
        return $this->status_pagamento === self::STATUS_PROCESSANDO;
    }

    public function getValorPagamentoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_pagamento, 2, ',', '.');
    }

    public function getValorLiquidoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_liquido, 2, ',', '.');
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_PROCESSANDO => '<span class="badge bg-warning">Processando</span>',
            self::STATUS_CONFIRMADO => '<span class="badge bg-success">Confirmado</span>',
            self::STATUS_CANCELADO => '<span class="badge bg-danger">Cancelado</span>',
            self::STATUS_ESTORNADO => '<span class="badge bg-secondary">Estornado</span>',
        ];

        return $badges[$this->status_pagamento] ?? '<span class="badge bg-light">Indefinido</span>';
    }

    /**
     * Métodos de negócio
     */
    public function calcularValorLiquido(): void
    {
        $this->valor_liquido = $this->valor_pagamento - $this->valor_taxa;
        $this->save();
    }

    public function calcularTaxa(): void
    {
        if ($this->taxa_percentual > 0) {
            $this->valor_taxa = ($this->valor_pagamento * $this->taxa_percentual) / 100;
            $this->calcularValorLiquido();
        }
    }

    public function calcularParcelas(): void
    {
        if ($this->parcelas > 1) {
            $this->valor_parcela = $this->valor_pagamento / $this->parcelas;
            $this->save();
        } else {
            $this->valor_parcela = $this->valor_pagamento;
            $this->save();
        }
    }

    /**
     * Boot method para eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendaPagamento) {
            // Definir data do pagamento se não especificada
            if (empty($vendaPagamento->data_pagamento)) {
                $vendaPagamento->data_pagamento = now();
            }
        });

        static::saved(function ($vendaPagamento) {
            // Recalcular taxa e parcelas
            $vendaPagamento->calcularTaxa();
            $vendaPagamento->calcularParcelas();
            
            // Atualizar status de pagamento da venda
            if ($vendaPagamento->venda) {
                $venda = $vendaPagamento->venda;
                $totalPagamentos = $venda->pagamentos()
                    ->where('status_pagamento', self::STATUS_CONFIRMADO)
                    ->sum('valor_pagamento');
                
                if ($totalPagamentos >= $venda->valor_total) {
                    $venda->status_pagamento = Venda::PAGAMENTO_PAGO;
                } elseif ($totalPagamentos > 0) {
                    $venda->status_pagamento = Venda::PAGAMENTO_PARCIAL;
                } else {
                    $venda->status_pagamento = Venda::PAGAMENTO_PENDENTE;
                }
                
                $venda->save();
            }
        });
    }
}