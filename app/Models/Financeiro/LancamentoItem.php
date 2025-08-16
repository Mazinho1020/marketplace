<?php

namespace App\Models\Financeiro;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model de Itens de Lançamentos
 * 
 * Gerencia os itens/produtos relacionados aos lançamentos financeiros
 * 
 * @property int $id
 * @property int $lancamento_id
 * @property int|null $produto_id
 * @property string $nome_produto
 * @property float $quantidade
 * @property float $valor_unitario
 * @property float $valor_total
 */
class LancamentoItem extends Model
{
    use HasFactory;

    protected $table = 'lancamento_itens';

    protected $fillable = [
        'lancamento_id',
        'produto_id',
        'produto_variacao_id',
        'codigo_produto',
        'nome_produto',
        'quantidade',
        'valor_unitario',
        'valor_desconto_item',
        'valor_total',
        'observacoes',
        'metadados',
        'empresa_id',
    ];

    protected $casts = [
        'quantidade' => 'decimal:4',
        'valor_unitario' => 'decimal:4',
        'valor_desconto_item' => 'decimal:4',
        'valor_total' => 'decimal:4',
        'metadados' => 'json',
    ];

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Calcular valor total automaticamente
        static::creating(function ($model) {
            $model->calcularValorTotal();
        });

        static::updating(function ($model) {
            $model->calcularValorTotal();
        });
    }

    /**
     * Relacionamentos
     */
    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(Lancamento::class, 'lancamento_id');
    }

    /**
     * Métodos de cálculo
     */
    public function calcularValorTotal()
    {
        $this->valor_total = ($this->quantidade * $this->valor_unitario) - $this->valor_desconto_item;
    }

    /**
     * Métodos de utilidade
     */
    public function temDesconto(): bool
    {
        return $this->valor_desconto_item > 0;
    }

    public function getPercentualDesconto(): float
    {
        if ($this->valor_unitario == 0) {
            return 0;
        }

        return ($this->valor_desconto_item / ($this->quantidade * $this->valor_unitario)) * 100;
    }

    /**
     * Formatters para exibição
     */
    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getQuantidadeFormatadaAttribute(): string
    {
        return number_format($this->quantidade, 2, ',', '.');
    }

    /**
     * Scopes
     */
    public function scopePorLancamento($query, $lancamentoId)
    {
        return $query->where('lancamento_id', $lancamentoId);
    }

    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('produto_id', $produtoId);
    }

    public function scopeComDesconto($query)
    {
        return $query->where('valor_desconto_item', '>', 0);
    }
}
