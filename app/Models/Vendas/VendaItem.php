<?php

namespace App\Models\Vendas;

use App\Models\Financial\BaseFinancialModel;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Itens de Venda
 * 
 * Representa os produtos vendidos em cada venda
 * 
 * @property int $id
 * @property int $venda_id
 * @property int $produto_id
 * @property int $produto_variacao_id
 * @property float $quantidade
 * @property float $valor_unitario
 * @property float $valor_total
 * @property float $desconto_unitario
 * @property float $desconto_total
 * @property string $observacoes
 * @property array $metadados
 * @property int $empresa_id
 */
class VendaItem extends BaseFinancialModel
{
    protected $table = 'venda_itens';

    protected $fillable = [
        'venda_id',
        'produto_id',
        'produto_variacao_id',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'desconto_unitario',
        'desconto_total',
        'observacoes',
        'metadados',
        'empresa_id'
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:4',
        'valor_total' => 'decimal:4',
        'desconto_unitario' => 'decimal:4',
        'desconto_total' => 'decimal:4',
        'metadados' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            // Calcular valor total se não foi fornecido
            if (empty($item->valor_total)) {
                $valorSemDesconto = $item->quantidade * $item->valor_unitario;
                $item->valor_total = $valorSemDesconto - ($item->desconto_total ?? 0);
            }
        });

        static::saved(function ($item) {
            // Recalcular totais da venda quando um item for alterado
            if ($item->venda) {
                $item->venda->recalcularTotais();
            }
        });

        static::deleted(function ($item) {
            // Recalcular totais da venda quando um item for removido
            if ($item->venda) {
                $item->venda->recalcularTotais();
            }

            // Restaurar estoque se o produto controla estoque
            if ($item->produto && $item->produto->controla_estoque) {
                $item->produto->increment('estoque_atual', $item->quantidade);
            }
        });
    }

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
        return $this->belongsTo(\App\Models\ProdutoVariacaoCombinacao::class, 'produto_variacao_id');
    }

    /**
     * Métodos de negócio
     */
    public function aplicarDesconto(float $desconto, string $tipo = 'valor'): void
    {
        if ($tipo === 'percentual') {
            $this->desconto_total = ($this->quantidade * $this->valor_unitario) * ($desconto / 100);
        } else {
            $this->desconto_total = $desconto;
        }

        $this->desconto_unitario = $this->desconto_total / $this->quantidade;
        $this->valor_total = ($this->quantidade * $this->valor_unitario) - $this->desconto_total;
        
        $this->save();
    }

    public function removerDesconto(): void
    {
        $this->desconto_unitario = 0;
        $this->desconto_total = 0;
        $this->valor_total = $this->quantidade * $this->valor_unitario;
        
        $this->save();
    }

    public function alterarQuantidade(float $novaQuantidade): void
    {
        // Atualizar estoque do produto se controla estoque
        if ($this->produto && $this->produto->controla_estoque) {
            $diferenca = $novaQuantidade - $this->quantidade;
            $this->produto->decrement('estoque_atual', $diferenca);
        }

        $this->quantidade = $novaQuantidade;
        $this->valor_total = ($this->quantidade * $this->valor_unitario) - ($this->desconto_total ?? 0);
        
        $this->save();
    }

    /**
     * Formatters
     */
    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getDescontoTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->desconto_total ?? 0, 2, ',', '.');
    }

    public function getQuantidadeFormatadaAttribute(): string
    {
        return number_format($this->quantidade, 2, ',', '.');
    }

    public function getNomeProdutoAttribute(): string
    {
        $nome = $this->produto ? $this->produto->nome : 'Produto não encontrado';
        
        if ($this->produtoVariacao) {
            $nome .= ' (' . $this->produtoVariacao->nome . ')';
        }
        
        return $nome;
    }

    /**
     * Cálculos
     */
    public function getValorBrutoAttribute(): float
    {
        return $this->quantidade * $this->valor_unitario;
    }

    public function getPercentualDescontoAttribute(): float
    {
        if ($this->valor_bruto == 0) {
            return 0;
        }

        return (($this->desconto_total ?? 0) / $this->valor_bruto) * 100;
    }

    public function getPossuiDescontoAttribute(): bool
    {
        return ($this->desconto_total ?? 0) > 0;
    }
}