<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoHistoricoPreco extends Model
{
    protected $table = 'produto_historico_precos';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'variacao_id',
        'preco_compra_anterior',
        'preco_compra_novo',
        'preco_venda_anterior',
        'preco_venda_novo',
        'margem_anterior',
        'margem_nova',
        'motivo',
        'usuario_id',
        'data_alteracao',
        'sync_status'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}

class ProdutoFornecedor extends Model
{
    protected $table = 'produto_fornecedores';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'fornecedor_pessoa_id',
        'codigo_fornecedor',
        'preco_compra',
        'prazo_entrega',
        'quantidade_minima',
        'desconto_percentual',
        'principal',
        'ativo',
        'sync_status'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}

class ProdutoCodigoBarras extends Model
{
    protected $table = 'produto_codigos_barras';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'variacao_id',
        'codigo',
        'tipo',
        'principal',
        'ativo',
        'sync_status'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}

class ProdutoPrecoQuantidade extends Model
{
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

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}

class ProdutoRelacionado extends Model
{
    protected $table = 'produto_relacionados';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'produto_relacionado_id',
        'tipo_relacao',
        'ordem',
        'ativo',
        'sync_status'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function produtoRelacionado()
    {
        return $this->belongsTo(Produto::class, 'produto_relacionado_id');
    }
}

class ProdutoKit extends Model
{
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
        'sync_status'
    ];

    public function produtoPrincipal()
    {
        return $this->belongsTo(Produto::class, 'produto_principal_id');
    }

    public function produtoItem()
    {
        return $this->belongsTo(Produto::class, 'produto_item_id');
    }
}

class Pessoa extends Model
{
    // Model básico para pessoa (já deve existir)
    protected $fillable = ['nome', 'email', 'tipo'];
}
