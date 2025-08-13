<?php

namespace App\Models\Financial;

use App\Models\Financial\BaseFinancialModel;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends BaseFinancialModel
{
    use HasSync, HasCompany;

    protected $table = 'tipo';

    protected $fillable = [
        'nome',
        'value',
        'empresa_id',
        'sync_status',
        'sync_hash',
        'sync_data',
    ];

    protected $casts = [
        'sync_data' => 'datetime',
    ];

    /**
     * Relacionamentos
     */
    public function classificacoesDre(): HasMany
    {
        return $this->hasMany(ClassificacaoDre::class, 'tipo_id');
    }

    public function contasGerenciais(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'tipo_id');
    }

    /**
     * Scopes
     */
    public function scopeComValue($query, string $value)
    {
        return $query->where('value', $value);
    }

    /**
     * Métodos auxiliares
     */
    public function getNomeCompletoAttribute(): string
    {
        return $this->value ? "{$this->nome} ({$this->value})" : $this->nome;
    }
}
