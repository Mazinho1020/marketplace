<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;

class BaseFinancialModel extends Model
{
    /**
     * Campos que podem ser assignados em massa
     */
    protected $guarded = ['id'];

    /**
     * Casts dos campos
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'ativo' => 'boolean',
    ];

    /**
     * Scope para buscar apenas registros ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para buscar apenas registros inativos
     */
    public function scopeInativos($query)
    {
        return $query->where('ativo', false);
    }

    /**
     * Scope para ordenação padrão
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('nome');
    }

    /**
     * Scope para busca de texto
     */
    public function scopeBuscar($query, string $termo, array $campos = ['nome'])
    {
        return $query->where(function ($q) use ($termo, $campos) {
            foreach ($campos as $campo) {
                $q->orWhere($campo, 'LIKE', "%{$termo}%");
            }
        });
    }
}
