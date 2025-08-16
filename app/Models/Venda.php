<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Modelo de Vendas do Marketplace
 * 
 * Gerencia todas as vendas realizadas através dos diferentes canais:
 * - PDV (Ponto de Venda)
 * - Delivery
 * - Balcão
 * - Online
 * - WhatsApp
 * 
 * @property int $id
 * @property string $uuid
 * @property int $empresa_id
 * @property int $usuario_id
 * @property int|null $cliente_id
 * @property string $numero_venda
 * @property string $tipo_venda
 * @property string $origem
 * @property float $subtotal
 * @property float $valor_total
 * @property string $status_venda
 * @property string $status_pagamento
 * @property Carbon $data_venda
 */
class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'empresa_id',
        'usuario_id',
        'cliente_id',
        'caixa_id',
        'mesa_id',
        'numero_venda',
        'tipo_venda',
        'origem',
        'subtotal',
        'desconto_percentual',
        'desconto_valor',
        'acrescimo_percentual',
        'acrescimo_valor',
        'total_impostos',
        'total_comissao',
        'valor_total',
        'status_venda',
        'status_pagamento',
        'status_entrega',
        'data_venda',
        'data_entrega_prevista',
        'data_entrega_realizada',
        'observacoes',
        'observacoes_internas',
        'cupom_desconto',
        'dados_entrega',
        'metadados',
        'percentual_comissao',
        'valor_comissao_marketplace',
        'comissao_calculada',
        'nf_gerada',
        'nf_numero',
        'nf_chave',
        'nf_data_emissao',
        'nf_xml_path'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'desconto_percentual' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
        'acrescimo_percentual' => 'decimal:2',
        'acrescimo_valor' => 'decimal:2',
        'total_impostos' => 'decimal:2',
        'total_comissao' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'percentual_comissao' => 'decimal:4',
        'valor_comissao_marketplace' => 'decimal:2',
        'comissao_calculada' => 'boolean',
        'nf_gerada' => 'boolean',
        'data_venda' => 'datetime',
        'data_entrega_prevista' => 'datetime',
        'data_entrega_realizada' => 'datetime',
        'nf_data_emissao' => 'datetime',
        'dados_entrega' => 'array',
        'metadados' => 'array',
    ];

    // Status possíveis
    const STATUS_ORCAMENTO = 'orcamento';
    const STATUS_PENDENTE = 'pendente';
    const STATUS_CONFIRMADA = 'confirmada';
    const STATUS_PAGA = 'paga';
    const STATUS_ENTREGUE = 'entregue';
    const STATUS_FINALIZADA = 'finalizada';
    const STATUS_CANCELADA = 'cancelada';

    const PAGAMENTO_PENDENTE = 'pendente';
    const PAGAMENTO_PARCIAL = 'parcial';
    const PAGAMENTO_PAGO = 'pago';
    const PAGAMENTO_ESTORNADO = 'estornado';

    const ENTREGA_PENDENTE = 'pendente';
    const ENTREGA_PREPARANDO = 'preparando';
    const ENTREGA_PRONTO = 'pronto';
    const ENTREGA_SAIU = 'saiu_entrega';
    const ENTREGA_ENTREGUE = 'entregue';
    const ENTREGA_CANCELADO = 'cancelado';

    /**
     * Relacionamentos
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(VendaItem::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(VendaPagamento::class);
    }

    /**
     * Scopes
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeConfirmadas($query)
    {
        return $query->whereIn('status_venda', [
            self::STATUS_CONFIRMADA, 
            self::STATUS_PAGA, 
            self::STATUS_ENTREGUE, 
            self::STATUS_FINALIZADA
        ]);
    }

    public function scopePagas($query)
    {
        return $query->where('status_pagamento', self::PAGAMENTO_PAGO);
    }

    public function scopeEntregues($query)
    {
        return $query->where('status_entrega', self::ENTREGA_ENTREGUE);
    }

    public function scopePorPeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_venda', [$dataInicio, $dataFim]);
    }

    public function scopePorVendedor($query, $vendedorId)
    {
        return $query->where('usuario_id', $vendedorId);
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_venda', $tipo);
    }

    /**
     * Métodos utilitários
     */
    public function isConfirmada(): bool
    {
        return in_array($this->status_venda, [
            self::STATUS_CONFIRMADA,
            self::STATUS_PAGA,
            self::STATUS_ENTREGUE,
            self::STATUS_FINALIZADA
        ]);
    }

    public function isPaga(): bool
    {
        return $this->status_pagamento === self::PAGAMENTO_PAGO;
    }

    public function isEntregue(): bool
    {
        return $this->status_entrega === self::ENTREGA_ENTREGUE;
    }

    public function isCancelada(): bool
    {
        return $this->status_venda === self::STATUS_CANCELADA;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            self::STATUS_ORCAMENTO => '<span class="badge bg-secondary">Orçamento</span>',
            self::STATUS_PENDENTE => '<span class="badge bg-warning">Pendente</span>',
            self::STATUS_CONFIRMADA => '<span class="badge bg-info">Confirmada</span>',
            self::STATUS_PAGA => '<span class="badge bg-success">Paga</span>',
            self::STATUS_ENTREGUE => '<span class="badge bg-primary">Entregue</span>',
            self::STATUS_FINALIZADA => '<span class="badge bg-success">Finalizada</span>',
            self::STATUS_CANCELADA => '<span class="badge bg-danger">Cancelada</span>',
        ];

        return $badges[$this->status_venda] ?? '<span class="badge bg-light">Indefinido</span>';
    }

    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getSubtotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    /**
     * Métodos de negócio
     */
    public function calcularTotais(): void
    {
        $this->subtotal = $this->itens->sum('valor_total_item');
        
        // Aplicar descontos
        if ($this->desconto_percentual > 0) {
            $this->desconto_valor = ($this->subtotal * $this->desconto_percentual) / 100;
        }
        
        // Aplicar acréscimos
        if ($this->acrescimo_percentual > 0) {
            $this->acrescimo_valor = ($this->subtotal * $this->acrescimo_percentual) / 100;
        }
        
        // Calcular total
        $this->valor_total = $this->subtotal - $this->desconto_valor + $this->acrescimo_valor;
        
        // Calcular comissão do marketplace
        if ($this->percentual_comissao > 0) {
            $this->valor_comissao_marketplace = ($this->valor_total * $this->percentual_comissao) / 100;
            $this->comissao_calculada = true;
        }
        
        $this->save();
    }

    public function confirmarVenda(): bool
    {
        if ($this->status_venda !== self::STATUS_PENDENTE) {
            return false;
        }

        $this->status_venda = self::STATUS_CONFIRMADA;
        $this->save();

        // Baixar estoque dos produtos
        foreach ($this->itens as $item) {
            $produto = $item->produto;
            if ($produto && !$item->estoque_baixado) {
                $produto->baixarEstoque($item->quantidade, 'venda', "Venda #{$this->numero_venda}");
                $item->estoque_baixado = true;
                $item->data_baixa_estoque = now();
                $item->save();
            }
        }

        return true;
    }

    public function cancelarVenda(): bool
    {
        if ($this->isCancelada()) {
            return false;
        }

        // Devolver estoque se já foi baixado
        foreach ($this->itens as $item) {
            if ($item->estoque_baixado) {
                $produto = $item->produto;
                if ($produto) {
                    $produto->adicionarEstoque($item->quantidade, 'cancelamento', "Cancelamento venda #{$this->numero_venda}");
                }
                $item->estoque_baixado = false;
                $item->data_baixa_estoque = null;
                $item->save();
            }
        }

        $this->status_venda = self::STATUS_CANCELADA;
        $this->save();

        return true;
    }

    public function gerarNumeroVenda(): string
    {
        $hoje = now()->format('Ymd');
        $ultimaVenda = static::where('empresa_id', $this->empresa_id)
            ->where('numero_venda', 'like', $hoje . '%')
            ->orderBy('numero_venda', 'desc')
            ->first();

        if ($ultimaVenda) {
            $ultimoSequencial = (int) substr($ultimaVenda->numero_venda, -4);
            $novoSequencial = $ultimoSequencial + 1;
        } else {
            $novoSequencial = 1;
        }

        return $hoje . str_pad($novoSequencial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method para eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venda) {
            // Gerar UUID
            if (empty($venda->uuid)) {
                $venda->uuid = (string) Str::uuid();
            }

            // Gerar número da venda
            if (empty($venda->numero_venda)) {
                $venda->numero_venda = $venda->gerarNumeroVenda();
            }

            // Definir data da venda se não especificada
            if (empty($venda->data_venda)) {
                $venda->data_venda = now();
            }
        });

        static::saved(function ($venda) {
            // Recalcular totais sempre que a venda for salva
            if ($venda->itens()->exists()) {
                $venda->calcularTotais();
            }
        });
    }
}