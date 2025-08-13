<?php

namespace App\Models\Financial;

use App\Models\Core\BaseModel;
use App\Enums\TipoContaEnum;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tipo extends BaseModel
{
    use HasSync, HasCompany;

    protected $table = 'tipo';

    protected $fillable = [
        'nome',
        'empresa_id',
        'value',
    ];

    protected $casts = [
        'value' => TipoContaEnum::class,
    ];

    // RELACIONAMENTOS
    public function classificacoesDre(): HasMany
    {
        return $this->hasMany(ClassificacaoDre::class, 'tipo_id');
    }

    public function contasGerenciais(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'tipo_id');
    }

    // SCOPES
    public function scopeReceitas($query)
    {
        return $query->where('value', TipoContaEnum::RECEITA);
    }

    public function scopeDespesas($query)
    {
        return $query->where('value', TipoContaEnum::DESPESA);
    }

    // ACCESSORS
    public function getIsReceitaAttribute(): bool
    {
        return $this->value === TipoContaEnum::RECEITA;
    }

    public function getIsDespesaAttribute(): bool
    {
        return $this->value === TipoContaEnum::DESPESA;
    }
}