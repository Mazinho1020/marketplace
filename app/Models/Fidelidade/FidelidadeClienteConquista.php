<?php

namespace App\Models\Fidelidade;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelidadeClienteConquista extends Model
{
    use SoftDeletes;

    protected $table = 'fidelidade_cliente_conquistas';

    protected $fillable = [
        'cliente_id',
        'conquista_id',
        'data_desbloqueio',
        'recompensa_resgatada'
    ];

    protected $casts = [
        'data_desbloqueio' => 'datetime',
        'recompensa_resgatada' => 'boolean'
    ];

    /**
     * Relacionamento com Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }

    /**
     * Relacionamento com Conquista
     */
    public function conquista(): BelongsTo
    {
        return $this->belongsTo(FidelidadeConquista::class, 'conquista_id');
    }

    /**
     * Scope para conquistas resgatadas
     */
    public function scopeResgatadas($query)
    {
        return $query->where('recompensa_resgatada', true);
    }

    /**
     * Scope para conquistas não resgatadas
     */
    public function scopeNaoResgatadas($query)
    {
        return $query->where('recompensa_resgatada', false);
    }

    /**
     * Marcar recompensa como resgatada
     */
    public function resgatar()
    {
        $this->update(['recompensa_resgatada' => true]);
    }
}
