<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FormaPagBandeira extends Model
{
    protected $table = 'forma_pag_bandeiras';

    protected $fillable = [
        'nome',
        'dias_para_receber',
        'taxa',
        'ativo',
        'empresa_id',
    ];

    protected $casts = [
        'taxa' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com recebimentos
     */
    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class, 'bandeira_id');
    }

    /**
     * Relacionamento com pagamentos
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'bandeira_id');
    }

    /**
     * Relacionamento com formas de pagamento através da tabela pivot
     */
    public function formasPagamento(): BelongsToMany
    {
        return $this->belongsToMany(
            FormaPagamento::class,
            'forma_pagamento_bandeiras',
            'bandeira_id',
            'forma_pagamento_id'
        );
    }

    /**
     * Scope para bandeiras ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para empresa específica
     */
    public function scopeEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
