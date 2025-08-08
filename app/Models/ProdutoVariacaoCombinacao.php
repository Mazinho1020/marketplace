<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoVariacaoCombinacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_variacoes_combinacoes';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'nome',
        'sku',
        'codigo_barras',
        'configuracoes',
        'preco_adicional',
        'preco_final',
        'estoque_proprio',
        'imagem',
        'ativo',
        'sync_status'
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'preco_adicional' => 'decimal:2',
        'preco_final' => 'decimal:2',
        'estoque_proprio' => 'decimal:3',
        'ativo' => 'boolean',
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

    public function imagens()
    {
        return $this->hasMany(ProdutoImagem::class, 'variacao_id');
    }

    public function movimentacoes()
    {
        return $this->hasMany(ProdutoMovimentacao::class, 'variacao_id');
    }
}
