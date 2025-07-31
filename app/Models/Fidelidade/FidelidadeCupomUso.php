<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelidadeCupomUso extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_cupons_uso';

    protected $fillable = [
        'cupom_id',
        'cliente_id',
        'pedido_id',
        'valor_desconto_aplicado',
        'data_uso'
    ];

    protected $casts = [
        'valor_desconto_aplicado' => 'decimal:2',
        'data_uso' => 'datetime'
    ];

    /**
     * Relacionamento com Cupom
     */
    public function cupom(): BelongsTo
    {
        return $this->belongsTo(FidelidadeCupom::class, 'cupom_id');
    }

    /**
     * Relacionamento com Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    /**
     * Relacionamento com Pedido
     */
    public function pedido()
    {
        return $this->belongsTo(\App\Models\PDV\Sale::class, 'pedido_id');
    }

    /**
     * Scope por período
     */
    public function scopePeriodo($query, $dataInicio, $dataFim)
    {
        return $query->whereBetween('data_uso', [$dataInicio, $dataFim]);
    }

    /**
     * Scope por cliente
     */
    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }
}
