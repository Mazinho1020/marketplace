<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ProdutoKit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produto_kits';

    protected $fillable = [
        'empresa_id',
        'produto_principal_id',
        'produto_item_id',
        'variacao_item_id',
        'quantidade',
        'preco_item',
        'desconto_percentual',
        'obrigatorio',
        'substituivel',
        'ordem',
        'ativo',
        'sync_status',
        'sync_data',
        'sync_hash'
    ];

    protected $casts = [
        'quantidade' => 'decimal:3',
        'preco_item' => 'decimal:2',
        'desconto_percentual' => 'decimal:2',
        'obrigatorio' => 'boolean',
        'substituivel' => 'boolean',
        'ativo' => 'boolean',
        'sync_data' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'sync_data'
    ];

    // Relacionamentos
    public function produtoPrincipal()
    {
        return $this->belongsTo(Produto::class, 'produto_principal_id');
    }

    public function produtoItem()
    {
        return $this->belongsTo(Produto::class, 'produto_item_id');
    }

    public function variacaoItem()
    {
        return $this->belongsTo(Produto::class, 'variacao_item_id'); // Simplificado por enquanto
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

    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }

    public function scopeSubstituiveis($query)
    {
        return $query->where('substituivel', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('ordem', 'asc')->orderBy('id', 'asc');
    }

    // Accessors
    public function getPrecoCalculadoAttribute()
    {
        $precoBase = $this->preco_item ?? $this->produtoItem->preco_venda ?? 0;

        if ($this->desconto_percentual > 0) {
            return $precoBase * (1 - ($this->desconto_percentual / 100));
        }

        return $precoBase;
    }

    public function getValorTotalAttribute()
    {
        return $this->preco_calculado * $this->quantidade;
    }

    public function getDescricaoComplementarAttribute()
    {
        $descricao = [];

        if ($this->quantidade != 1) {
            $descricao[] = "{$this->quantidade}x";
        }

        if ($this->desconto_percentual > 0) {
            $descricao[] = "Desconto: {$this->desconto_percentual}%";
        }

        if ($this->obrigatorio) {
            $descricao[] = "Obrigatório";
        }

        if ($this->substituivel) {
            $descricao[] = "Substituível";
        }

        return implode(' | ', $descricao);
    }

    // Métodos estáticos
    public static function calcularPrecoTotalKit($produtoId, $empresaId = null)
    {
        $query = self::where('produto_principal_id', $produtoId)
            ->with(['produtoItem', 'variacaoItem'])
            ->ativos();

        if ($empresaId) {
            $query->porEmpresa($empresaId);
        }

        $itens = $query->get();
        $precoTotal = 0;

        foreach ($itens as $item) {
            $precoTotal += $item->valor_total;
        }

        return $precoTotal;
    }

    public static function obterItensKit($produtoId, $empresaId = null)
    {
        $query = self::where('produto_principal_id', $produtoId)
            ->with(['produtoItem', 'variacaoItem'])
            ->ativos()
            ->ordenados();

        if ($empresaId) {
            $query->porEmpresa($empresaId);
        }

        return $query->get();
    }

    // Validações customizadas
    public function validarQuantidadeEstoque()
    {
        if (!$this->produtoItem->controla_estoque) {
            return true;
        }

        $estoqueDisponivel = $this->variacaoItem
            ? $this->variacaoItem->estoque_proprio ?? $this->produtoItem->estoque_atual
            : $this->produtoItem->estoque_atual;

        return $estoqueDisponivel >= $this->quantidade;
    }

    public function podeSerSubstituido()
    {
        return $this->substituivel && !$this->obrigatorio;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->empresa_id) {
                $model->empresa_id = Auth::user()->empresa_id;
            }

            $model->sync_status = 'pendente';
            $model->sync_data = now();
        });

        static::updating(function ($model) {
            $model->sync_status = 'pendente';
            $model->sync_data = now();
        });
    }
}
