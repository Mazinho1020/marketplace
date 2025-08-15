<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FormaPagamento extends Model
{
    protected $table = 'formas_pagamento';

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
        return $this->hasMany(Recebimento::class, 'forma_pagamento_id');
    }

    /**
     * Relacionamento com bandeiras através da tabela pivot
     */
    public function bandeiras(): BelongsToMany
    {
        return $this->belongsToMany(
            FormaPagBandeira::class,
            'forma_pagamento_bandeiras',
            'forma_pagamento_id',
            'bandeira_id'
        );
    }

    /**
     * Scope para formas ativas
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
