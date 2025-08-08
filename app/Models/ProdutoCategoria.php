<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdutoCategoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'categoria_pai_id',
        'nome',
        'descricao',
        'slug',
        'icone',
        'cor',
        'imagem',
        'ordem',
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

    public function categoriaPai()
    {
        return $this->belongsTo(ProdutoCategoria::class, 'categoria_pai_id');
    }

    public function subcategorias()
    {
        return $this->hasMany(ProdutoCategoria::class, 'categoria_pai_id');
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'categoria_id');
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePrincipais($query)
    {
        return $query->whereNull('categoria_pai_id');
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos auxiliares
    public function getQuantidadeProdutosAttribute()
    {
        return $this->produtos()->count();
    }
}
