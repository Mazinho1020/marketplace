<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoMovimentacao extends Model
{
    use HasFactory;

    protected $table = 'produto_movimentacoes';

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'variacao_id',
        'tipo',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'estoque_anterior',
        'estoque_posterior',
        'motivo',
        'observacoes',
        'documento',
        'fornecedor_id',
        'cliente_id',
        'usuario_id',
        'data_movimento',
        'sync_status'
    ];

    protected $casts = [
        'quantidade' => 'decimal:3',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'estoque_anterior' => 'decimal:3',
        'estoque_posterior' => 'decimal:3',
        'data_movimento' => 'datetime',
    ];

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

    public function fornecedor()
    {
        return $this->belongsTo(Pessoa::class, 'fornecedor_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    // Scopes
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    public function scopeSaidas($query)
    {
        return $query->where('tipo', 'saida');
    }

    public function scopeVendas($query)
    {
        return $query->where('tipo', 'venda');
    }
}
