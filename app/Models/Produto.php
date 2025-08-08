<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'subcategoria_id',
        'marca_id',
        'tipo',
        'possui_variacoes',
        'codigo_sistema',
        'nome',
        'nome_reduzido',
        'slug',
        'sku',
        'codigo_fabricante',
        'status',
        'status_venda',
        'ativo',
        'descricao',
        'descricao_curta',
        'especificacoes_tecnicas',
        'ingredientes',
        'informacoes_nutricionais',
        'modo_uso',
        'cuidados',
        'codigo_barras',
        'gtin',
        'ncm',
        'cest',
        'origem',
        'cfop',
        'preco_compra',
        'preco_venda',
        'preco_promocional',
        'margem_lucro',
        'controla_estoque',
        'estoque_atual',
        'estoque_minimo',
        'estoque_maximo',
        'unidade_medida',
        'unidade_compra',
        'fator_conversao',
        'peso_liquido',
        'peso_bruto',
        'altura',
        'largura',
        'profundidade',
        'volume',
        'cst',
        'aliquota_icms',
        'aliquota_ipi',
        'aliquota_pis',
        'aliquota_cofins',
        'observacoes',
        'palavras_chave',
        'ordem_exibicao',
        'destaque',
        'sync_status'
    ];

    protected $casts = [
        'possui_variacoes' => 'boolean',
        'ativo' => 'boolean',
        'preco_compra' => 'decimal:2',
        'preco_venda' => 'decimal:2',
        'preco_promocional' => 'decimal:2',
        'margem_lucro' => 'decimal:2',
        'controla_estoque' => 'boolean',
        'estoque_atual' => 'decimal:3',
        'estoque_minimo' => 'decimal:3',
        'estoque_maximo' => 'decimal:3',
        'fator_conversao' => 'decimal:4',
        'peso_liquido' => 'decimal:3',
        'peso_bruto' => 'decimal:3',
        'altura' => 'decimal:2',
        'largura' => 'decimal:2',
        'profundidade' => 'decimal:2',
        'volume' => 'decimal:3',
        'aliquota_icms' => 'decimal:2',
        'aliquota_ipi' => 'decimal:2',
        'aliquota_pis' => 'decimal:2',
        'aliquota_cofins' => 'decimal:2',
        'destaque' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria()
    {
        return $this->belongsTo(ProdutoCategoria::class, 'categoria_id');
    }

    public function subcategoria()
    {
        return $this->belongsTo(ProdutoSubcategoria::class, 'subcategoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(ProdutoMarca::class, 'marca_id');
    }

    public function imagens()
    {
        return $this->hasMany(ProdutoImagem::class);
    }

    public function imagemPrincipal()
    {
        return $this->hasOne(ProdutoImagem::class)->where('tipo', 'principal');
    }

    public function configuracoes()
    {
        return $this->hasMany(ProdutoConfiguracao::class);
    }

    public function variacoes()
    {
        return $this->hasMany(ProdutoVariacaoCombinacao::class);
    }

    public function movimentacoes()
    {
        return $this->hasMany(ProdutoMovimentacao::class);
    }

    public function historicoPrecos()
    {
        return $this->hasMany(ProdutoHistoricoPreco::class);
    }

    public function codigosBarras()
    {
        return $this->hasMany(ProdutoCodigoBarras::class);
    }

    public function precosQuantidade()
    {
        return $this->hasMany(ProdutoPrecoQuantidade::class);
    }

    public function relacionados()
    {
        return $this->hasMany(ProdutoRelacionado::class);
    }

    public function kits()
    {
        return $this->hasMany(ProdutoKit::class, 'produto_principal_id');
    }

    public function itensKit()
    {
        return $this->hasMany(ProdutoKit::class, 'produto_item_id');
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopeComEstoque($query)
    {
        return $query->where(function ($q) {
            $q->where('controla_estoque', false)
                ->orWhere('estoque_atual', '>', 0);
        });
    }

    public function scopeDestaque($query)
    {
        return $query->where('destaque', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Métodos auxiliares
    public function getPrecoVendaFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_venda, 2, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'disponivel' => '<span class="badge bg-success">Disponível</span>',
            'indisponivel' => '<span class="badge bg-danger">Indisponível</span>',
            'pausado' => '<span class="badge bg-warning">Pausado</span>',
            'esgotado' => '<span class="badge bg-secondary">Esgotado</span>',
            'novidade' => '<span class="badge bg-info">Novidade</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Indefinido</span>';
    }

    public function getEstoqueBaixoAttribute()
    {
        if (!$this->controla_estoque) {
            return false;
        }

        return $this->estoque_atual <= $this->estoque_minimo;
    }

    public function getUrlImagemPrincipalAttribute()
    {
        $imagem = $this->imagemPrincipal;
        if ($imagem) {
            return asset('storage/produtos/' . $imagem->arquivo);
        }

        return asset('images/produto-sem-foto.png');
    }

    public function calcularMargem()
    {
        if ($this->preco_compra > 0) {
            return (($this->preco_venda - $this->preco_compra) / $this->preco_compra) * 100;
        }

        return 0;
    }

    public function baixarEstoque($quantidade, $motivo = 'venda', $observacoes = null)
    {
        if (!$this->controla_estoque) {
            return true;
        }

        if ($this->estoque_atual < $quantidade) {
            return false;
        }

        $estoqueAnterior = $this->estoque_atual;
        $this->estoque_atual -= $quantidade;
        $this->save();

        // Registrar movimentação
        $this->movimentacoes()->create([
            'empresa_id' => $this->empresa_id,
            'tipo' => 'saida',
            'quantidade' => $quantidade,
            'valor_unitario' => $this->preco_venda,
            'valor_total' => $quantidade * $this->preco_venda,
            'estoque_anterior' => $estoqueAnterior,
            'estoque_posterior' => $this->estoque_atual,
            'motivo' => $motivo,
            'observacoes' => $observacoes,
            'data_movimento' => now(),
            'sync_status' => 'pendente'
        ]);

        return true;
    }

    public function adicionarEstoque($quantidade, $motivo = 'entrada', $observacoes = null)
    {
        if (!$this->controla_estoque) {
            return true;
        }

        $estoqueAnterior = $this->estoque_atual;
        $this->estoque_atual += $quantidade;
        $this->save();

        // Registrar movimentação
        $this->movimentacoes()->create([
            'empresa_id' => $this->empresa_id,
            'tipo' => 'entrada',
            'quantidade' => $quantidade,
            'valor_unitario' => $this->preco_compra ?? 0,
            'valor_total' => $quantidade * ($this->preco_compra ?? 0),
            'estoque_anterior' => $estoqueAnterior,
            'estoque_posterior' => $this->estoque_atual,
            'motivo' => $motivo,
            'observacoes' => $observacoes,
            'data_movimento' => now(),
            'sync_status' => 'pendente'
        ]);

        return true;
    }

    // Boot method para eventos
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($produto) {
            // Gerar slug automaticamente
            if (empty($produto->slug)) {
                $produto->slug = Str::slug($produto->nome);
            }

            // Calcular margem automaticamente
            if ($produto->preco_compra > 0 && $produto->preco_venda > 0) {
                $produto->margem_lucro = (($produto->preco_venda - $produto->preco_compra) / $produto->preco_compra) * 100;
            }

            // Marcar para sincronização
            $produto->sync_status = 'pendente';
        });
    }
}
