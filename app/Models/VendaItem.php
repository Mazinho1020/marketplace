<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model VendaItem
 * 
 * Representa um item de venda no sistema seguindo o padrão definido em PADRONIZACAO_COMPLETA.md
 */
class VendaItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'venda_itens';

    // =========================================
    // CONSTANTES ORGANIZADAS POR CATEGORIA
    // =========================================

    // Status do item
    const STATUS_ITEM = [
        'ativo' => 'Ativo',
        'cancelado' => 'Cancelado',
        'devolvido' => 'Devolvido',
        'trocado' => 'Trocado'
    ];

    // Status de sincronização
    const SYNC_STATUS = [
        'pending' => 'Pendente',
        'synced' => 'Sincronizado',
        'error' => 'Erro',
        'ignored' => 'Ignorado'
    ];

    // =========================================
    // CAMPOS FILLABLE
    // =========================================

    protected $fillable = [
        'empresa_id',
        'venda_id',
        'produto_id',
        'produto_variacao_id',
        'codigo_produto',
        'nome_produto',
        'descricao_produto',
        'unidade_medida',
        'quantidade',
        'valor_unitario',
        'valor_unitario_original',
        'valor_desconto_item',
        'percentual_desconto',
        'valor_acrescimo_item',
        'valor_total_item',
        'valor_custo_unitario',
        'valor_custo_total',
        'margem_lucro',
        'valor_impostos_item',
        'aliquota_icms',
        'aliquota_ipi',
        'aliquota_pis',
        'aliquota_cofins',
        'controla_estoque',
        'estoque_anterior',
        'estoque_posterior',
        'ncm',
        'cest',
        'cfop',
        'observacoes',
        'metadados',
        'caracteristicas_produto',
        'item_kit',
        'kit_pai_id',
        'ordem_item',
        'quantidade_devolvida',
        'quantidade_cancelada',
        'status_item',
        'sync_hash',
        'sync_status',
        'sync_data'
    ];

    // =========================================
    // CASTS
    // =========================================

    protected $casts = [
        'quantidade' => 'decimal:3',
        'valor_unitario' => 'decimal:2',
        'valor_unitario_original' => 'decimal:2',
        'valor_desconto_item' => 'decimal:2',
        'percentual_desconto' => 'decimal:2',
        'valor_acrescimo_item' => 'decimal:2',
        'valor_total_item' => 'decimal:2',
        'valor_custo_unitario' => 'decimal:2',
        'valor_custo_total' => 'decimal:2',
        'margem_lucro' => 'decimal:2',
        'valor_impostos_item' => 'decimal:2',
        'aliquota_icms' => 'decimal:2',
        'aliquota_ipi' => 'decimal:2',
        'aliquota_pis' => 'decimal:2',
        'aliquota_cofins' => 'decimal:2',
        'controla_estoque' => 'boolean',
        'estoque_anterior' => 'decimal:3',
        'estoque_posterior' => 'decimal:3',
        'item_kit' => 'boolean',
        'quantidade_devolvida' => 'decimal:3',
        'quantidade_cancelada' => 'decimal:3',
        'sync_data' => 'datetime',
        'metadados' => 'array',
        'caracteristicas_produto' => 'array'
    ];

    // =========================================
    // RELACIONAMENTOS
    // =========================================

    /**
     * Relacionamento com Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relacionamento com Venda
     */
    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    /**
     * Relacionamento com Produto
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Relacionamento com item pai do kit
     */
    public function kitPai(): BelongsTo
    {
        return $this->belongsTo(VendaItem::class, 'kit_pai_id');
    }

    /**
     * Relacionamento com itens filhos do kit
     */
    public function itensKit()
    {
        return $this->hasMany(VendaItem::class, 'kit_pai_id');
    }

    // =========================================
    // SCOPES
    // =========================================

    /**
     * Scope para itens da empresa
     */
    public function scopePorEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para itens ativos
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('status_item', 'ativo');
    }

    /**
     * Scope para itens cancelados
     */
    public function scopeCancelados(Builder $query): Builder
    {
        return $query->where('status_item', 'cancelado');
    }

    /**
     * Scope para itens devolvidos
     */
    public function scopeDevolvidos(Builder $query): Builder
    {
        return $query->where('status_item', 'devolvido');
    }

    /**
     * Scope para itens de kit
     */
    public function scopeItensKit(Builder $query): Builder
    {
        return $query->where('item_kit', true);
    }

    /**
     * Scope para itens principais (não de kit)
     */
    public function scopeItensPrincipais(Builder $query): Builder
    {
        return $query->where('item_kit', false);
    }

    /**
     * Scope para itens por produto
     */
    public function scopePorProduto(Builder $query, int $produtoId): Builder
    {
        return $query->where('produto_id', $produtoId);
    }

    // =========================================
    // ACCESSORS
    // =========================================

    /**
     * Accessor para valor total formatado
     */
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total_item, 2, ',', '.');
    }

    /**
     * Accessor para valor unitário formatado
     */
    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    /**
     * Accessor para status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'ativo' => '<span class="badge bg-success">Ativo</span>',
            'cancelado' => '<span class="badge bg-danger">Cancelado</span>',
            'devolvido' => '<span class="badge bg-warning">Devolvido</span>',
            'trocado' => '<span class="badge bg-info">Trocado</span>',
        ];

        return $badges[$this->status_item] ?? '<span class="badge bg-light">Indefinido</span>';
    }

    /**
     * Accessor para quantidade líquida (vendida - devolvida - cancelada)
     */
    public function getQuantidadeLiquidaAttribute(): float
    {
        return $this->quantidade - $this->quantidade_devolvida - $this->quantidade_cancelada;
    }

    /**
     * Accessor para valor líquido do item
     */
    public function getValorLiquidoAttribute(): float
    {
        return $this->quantidade_liquida * $this->valor_unitario;
    }

    // =========================================
    // MÉTODOS DE NEGÓCIO
    // =========================================

    /**
     * Calcula o total do item baseado na quantidade e valor unitário
     */
    public function calcularTotalItem(): void
    {
        $this->valor_total_item = $this->quantidade * $this->valor_unitario;
        
        if ($this->valor_custo_unitario) {
            $this->valor_custo_total = $this->quantidade * $this->valor_custo_unitario;
            $this->calcularMargemLucro();
        }
    }

    /**
     * Calcula a margem de lucro
     */
    public function calcularMargemLucro(): void
    {
        if ($this->valor_custo_unitario > 0) {
            $this->margem_lucro = (($this->valor_unitario - $this->valor_custo_unitario) / $this->valor_custo_unitario) * 100;
        }
    }

    /**
     * Aplica desconto no item
     */
    public function aplicarDesconto(float $valor, bool $percentual = false): void
    {
        if ($percentual) {
            $this->percentual_desconto = $valor;
            $this->valor_desconto_item = ($this->valor_unitario_original * $valor) / 100;
        } else {
            $this->valor_desconto_item = $valor;
            if ($this->valor_unitario_original > 0) {
                $this->percentual_desconto = ($valor / $this->valor_unitario_original) * 100;
            }
        }

        $this->valor_unitario = $this->valor_unitario_original - $this->valor_desconto_item;
        $this->calcularTotalItem();
    }

    /**
     * Cancela uma quantidade do item
     */
    public function cancelarQuantidade(float $quantidade): bool
    {
        if ($quantidade > ($this->quantidade - $this->quantidade_cancelada)) {
            return false;
        }

        $this->quantidade_cancelada += $quantidade;
        
        if ($this->quantidade_cancelada >= $this->quantidade) {
            $this->status_item = 'cancelado';
        }

        return $this->save();
    }

    /**
     * Devolve uma quantidade do item
     */
    public function devolverQuantidade(float $quantidade): bool
    {
        if ($quantidade > ($this->quantidade - $this->quantidade_devolvida - $this->quantidade_cancelada)) {
            return false;
        }

        $this->quantidade_devolvida += $quantidade;
        
        if ($this->quantidade_devolvida >= $this->quantidade) {
            $this->status_item = 'devolvido';
        }

        return $this->save();
    }

    /**
     * Baixa o estoque do produto relacionado
     */
    public function baixarEstoque(): bool
    {
        if (!$this->controla_estoque || !$this->produto) {
            return true;
        }

        $this->estoque_anterior = $this->produto->estoque_atual;
        
        $sucesso = $this->produto->baixarEstoque(
            $this->quantidade,
            'venda',
            "Venda #{$this->venda->numero_venda} - Item #{$this->id}"
        );

        if ($sucesso) {
            $this->estoque_posterior = $this->produto->estoque_atual;
            $this->save();
        }

        return $sucesso;
    }

    /**
     * Reverte o estoque do produto relacionado
     */
    public function reverterEstoque(): bool
    {
        if (!$this->controla_estoque || !$this->produto) {
            return true;
        }

        return $this->produto->adicionarEstoque(
            $this->quantidade,
            'cancelamento',
            "Cancelamento da venda #{$this->venda->numero_venda} - Item #{$this->id}"
        );
    }

    // =========================================
    // EVENTS
    // =========================================

    /**
     * Boot method para eventos do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // Marcar para sincronização
            $item->sync_status = 'pending';
            $item->sync_hash = md5(json_encode($item->toArray()));
        });

        static::updating(function ($item) {
            // Atualizar hash de sincronização
            $item->sync_status = 'pending';
            $item->sync_hash = md5(json_encode($item->toArray()));
        });

        static::saved(function ($item) {
            // Recalcular total da venda quando o item for salvo
            if ($item->venda) {
                $item->venda->calcularTotal();
                $item->venda->save();
            }
        });
    }
}
