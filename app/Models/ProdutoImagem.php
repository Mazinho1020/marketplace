<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoImagem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_imagens';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'variacao_id',
        'tipo',
        'arquivo',
        'titulo',
        'alt_text',
        'ordem',
        'tamanho_arquivo',
        'dimensoes',
        'ativo',
        'sync_status'
    ];

    protected $casts = [
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

    public function variacao()
    {
        return $this->belongsTo(ProdutoVariacaoCombinacao::class, 'variacao_id');
    }

    // Métodos auxiliares
    public function getUrlAttribute()
    {
        return asset('storage/produtos/' . $this->arquivo);
    }

    public function getUrlMiniatura($width = 150, $height = 150)
    {
        // Implementar lógica de redimensionamento se necessário
        return $this->url;
    }
}
