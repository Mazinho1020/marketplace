<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoPrecoQuantidade extends Model
{
    use SoftDeletes;

    protected $table = 'produto_precos_quantidade';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'variacao_id',
        'quantidade_minima',
        'quantidade_maxima',
        'preco',
        'desconto_percentual',
        'ativo',
        'sync_status'
    ];

    protected $casts = [
        'quantidade_minima' => 'decimal:3',
        'quantidade_maxima' => 'decimal:3',
        'preco' => 'decimal:2',
        'desconto_percentual' => 'decimal:2',
        'ativo' => 'boolean'
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function variacao()
    {
        return $this->belongsTo(ProdutoVariacaoCombinacao::class, 'variacao_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('produto_id', $produtoId);
    }

    // Métodos auxiliares
    public function getPrecoComDescontoAttribute()
    {
        if ($this->desconto_percentual > 0) {
            return $this->preco * (1 - ($this->desconto_percentual / 100));
        }
        return $this->preco;
    }

    public function getEconomiaAttribute()
    {
        if ($this->desconto_percentual > 0) {
            return $this->preco - $this->preco_com_desconto;
        }
        return 0;
    }

    public function aplicavelParaQuantidade($quantidade)
    {
        return $quantidade >= $this->quantidade_minima &&
            ($this->quantidade_maxima === null || $quantidade <= $this->quantidade_maxima);
    }
}
