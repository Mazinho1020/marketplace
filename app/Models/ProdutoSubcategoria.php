<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProdutoSubcategoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_subcategorias';

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'parent_id',
        'nome',
        'descricao',
        'slug',
        'icone',
        'cor_fundo',
        'imagem_url',
        'ordem',
        'ativo',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'sync_status'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria()
    {
        return $this->belongsTo(ProdutoCategoria::class, 'categoria_id');
    }

    // Relacionamento hierárquico - subcategoria pai
    public function parent()
    {
        return $this->belongsTo(ProdutoSubcategoria::class, 'parent_id');
    }

    // Relacionamento hierárquico - subcategorias filhas
    public function children()
    {
        return $this->hasMany(ProdutoSubcategoria::class, 'parent_id')->orderBy('ordem');
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'subcategoria_id');
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePrincipais($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeFilhas($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('ordem')->orderBy('nome');
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    // Métodos automáticos
    public static function boot()
    {
        parent::boot();

        static::creating(function ($subcategoria) {
            if (empty($subcategoria->slug)) {
                $subcategoria->slug = Str::slug($subcategoria->nome);
            }

            if (empty($subcategoria->ordem)) {
                $ultimaOrdem = static::where('empresa_id', $subcategoria->empresa_id)
                    ->where('categoria_id', $subcategoria->categoria_id)
                    ->where('parent_id', $subcategoria->parent_id)
                    ->max('ordem');
                $subcategoria->ordem = $ultimaOrdem ? $ultimaOrdem + 1 : 1;
            }
        });

        static::updating(function ($subcategoria) {
            if ($subcategoria->isDirty('nome') && empty($subcategoria->slug)) {
                $subcategoria->slug = Str::slug($subcategoria->nome);
            }
        });
    }

    // Métodos úteis
    public function getCaminhoCompleto()
    {
        $caminho = collect([$this->nome]);

        $parent = $this->parent;
        while ($parent) {
            $caminho->prepend($parent->nome);
            $parent = $parent->parent;
        }

        return $caminho->implode(' > ');
    }

    public function getTodosFilhos()
    {
        $filhos = collect();

        foreach ($this->children as $filho) {
            $filhos->push($filho);
            $filhos = $filhos->merge($filho->getTodosFilhos());
        }

        return $filhos;
    }

    public function podeSerDeletada()
    {
        return $this->produtos()->count() === 0 && $this->children()->count() === 0;
    }

    public function getImagemUrlAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    // Métodos estáticos
    public static function getArvore($empresaId, $categoriaId = null)
    {
        $query = static::with('children.children.children')
            ->porEmpresa($empresaId)
            ->principais()
            ->ativas()
            ->ordenado();

        if ($categoriaId) {
            $query->porCategoria($categoriaId);
        }

        return $query->get();
    }

    public static function getOpcoesSelect($empresaId, $categoriaId = null, $incluirInativos = false)
    {
        $query = static::porEmpresa($empresaId);

        if ($categoriaId) {
            $query->porCategoria($categoriaId);
        }

        if (!$incluirInativos) {
            $query->ativas();
        }

        $subcategorias = $query->ordenado()->get();
        $opcoes = collect();

        foreach ($subcategorias as $subcategoria) {
            static::adicionarOpcoesRecursivo($opcoes, $subcategoria, '');
        }

        return $opcoes->pluck('nome', 'id')->toArray();
    }

    private static function adicionarOpcoesRecursivo($opcoes, $subcategoria, $prefixo)
    {
        $opcoes->push([
            'id' => $subcategoria->id,
            'nome' => $prefixo . $subcategoria->nome
        ]);

        foreach ($subcategoria->children as $filho) {
            static::adicionarOpcoesRecursivo($opcoes, $filho, $prefixo . '-- ');
        }
    }
}
