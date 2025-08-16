<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Itens das Vendas
 * 
 * Representa cada produto/item vendido em uma venda
 * Mantém histórico dos preços e configurações no momento da venda
 * 
 * @property int $id
 * @property int $venda_id
 * @property int $produto_id
 * @property int|null $produto_variacao_id
 * @property string $nome_produto
 * @property float $quantidade
 * @property float $valor_unitario
 * @property float $valor_total_item
 * @property float $margem_lucro
 */
class VendaItem extends Model
{
    use HasFactory;

    protected $table = 'venda_itens';

    protected $fillable = [
        'venda_id',
        'produto_id',
        'produto_variacao_id',
        'codigo_produto',
        'nome_produto',
        'quantidade',
        'valor_unitario',
        'valor_unitario_original',
        'desconto_percentual',
        'desconto_valor',
        'valor_total_item',
        'custo_unitario',
        'margem_lucro',
        'aliquota_icms',
        'valor_icms',
        'aliquota_ipi',
        'valor_ipi',
        'aliquota_pis',
        'valor_pis',
        'aliquota_cofins',
        'valor_cofins',
        'observacoes',
        'configuracoes',
        'personalizacoes',
        'estoque_baixado',
        'data_baixa_estoque',
        'percentual_comissao_vendedor',
        'valor_comissao_vendedor',
        'empresa_id'
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:2',
        'valor_unitario_original' => 'decimal:2',
        'desconto_percentual' => 'decimal:2',
        'desconto_valor' => 'decimal:2',
        'valor_total_item' => 'decimal:2',
        'custo_unitario' => 'decimal:2',
        'margem_lucro' => 'decimal:2',
        'aliquota_icms' => 'decimal:2',
        'valor_icms' => 'decimal:2',
        'aliquota_ipi' => 'decimal:2',
        'valor_ipi' => 'decimal:2',
        'aliquota_pis' => 'decimal:2',
        'valor_pis' => 'decimal:2',
        'aliquota_cofins' => 'decimal:2',
        'valor_cofins' => 'decimal:2',
        'estoque_baixado' => 'boolean',
        'data_baixa_estoque' => 'datetime',
        'percentual_comissao_vendedor' => 'decimal:2',
        'valor_comissao_vendedor' => 'decimal:2',
        'configuracoes' => 'array',
        'personalizacoes' => 'array',
    ];

    /**
     * Relacionamentos
     */
    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function produtoVariacao(): BelongsTo
    {
        return $this->belongsTo(ProdutoVariacaoCombinacao::class, 'produto_variacao_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Scopes
     */
    public function scopePorVenda($query, $vendaId)
    {
        return $query->where('venda_id', $vendaId);
    }

    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('produto_id', $produtoId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeComEstoqueBaixado($query)
    {
        return $query->where('estoque_baixado', true);
    }

    /**
     * Métodos utilitários
     */
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total_item, 2, ',', '.');
    }

    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    public function getMargemLucroPercentualAttribute(): float
    {
        if ($this->custo_unitario > 0) {
            return (($this->valor_unitario - $this->custo_unitario) / $this->custo_unitario) * 100;
        }
        return 0;
    }

    public function getValorLucroAttribute(): float
    {
        return ($this->valor_unitario - $this->custo_unitario) * $this->quantidade;
    }

    /**
     * Métodos de negócio
     */
    public function calcularValorTotal(): void
    {
        $valorBruto = $this->quantidade * $this->valor_unitario;
        
        // Aplicar desconto se houver
        if ($this->desconto_percentual > 0) {
            $this->desconto_valor = ($valorBruto * $this->desconto_percentual) / 100;
        }
        
        $this->valor_total_item = $valorBruto - $this->desconto_valor;
        
        // Calcular comissão do vendedor
        if ($this->percentual_comissao_vendedor > 0) {
            $this->valor_comissao_vendedor = ($this->valor_total_item * $this->percentual_comissao_vendedor) / 100;
        }
        
        $this->save();
    }

    public function calcularImpostos(): void
    {
        $valorBase = $this->valor_total_item;
        
        // ICMS
        if ($this->aliquota_icms > 0) {
            $this->valor_icms = ($valorBase * $this->aliquota_icms) / 100;
        }
        
        // IPI
        if ($this->aliquota_ipi > 0) {
            $this->valor_ipi = ($valorBase * $this->aliquota_ipi) / 100;
        }
        
        // PIS
        if ($this->aliquota_pis > 0) {
            $this->valor_pis = ($valorBase * $this->aliquota_pis) / 100;
        }
        
        // COFINS
        if ($this->aliquota_cofins > 0) {
            $this->valor_cofins = ($valorBase * $this->aliquota_cofins) / 100;
        }
        
        $this->save();
    }

    public function carregarDadosProduto(): void
    {
        if ($this->produto) {
            $produto = $this->produto;
            
            // Carregar dados básicos
            $this->codigo_produto = $produto->codigo_sistema ?? $produto->sku;
            $this->nome_produto = $produto->nome;
            $this->valor_unitario_original = $produto->preco_venda;
            $this->custo_unitario = $produto->preco_compra ?? 0;
            
            // Carregar alíquotas fiscais
            $this->aliquota_icms = $produto->aliquota_icms ?? 0;
            $this->aliquota_ipi = $produto->aliquota_ipi ?? 0;
            $this->aliquota_pis = $produto->aliquota_pis ?? 0;
            $this->aliquota_cofins = $produto->aliquota_cofins ?? 0;
            
            // Se não foi definido valor unitário, usar o preço do produto
            if (!$this->valor_unitario) {
                $this->valor_unitario = $produto->preco_venda;
            }
            
            $this->save();
        }
    }

    /**
     * Boot method para eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendaItem) {
            // Carregar dados do produto automaticamente
            $vendaItem->carregarDadosProduto();
        });

        static::saved(function ($vendaItem) {
            // Recalcular totais sempre que o item for salvo
            $vendaItem->calcularValorTotal();
            $vendaItem->calcularImpostos();
            
            // Atualizar totais da venda
            if ($vendaItem->venda) {
                $vendaItem->venda->calcularTotais();
            }
        });
    }
}