<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model para a tabela empresas
 */
class Business extends Model
{
    protected $table = 'empresas';

    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'email',
        'telefone',
        'celular',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_abertura' => 'date',
        'optante_simples' => 'boolean',
        'incentivo_fiscal' => 'boolean'
    ];

    /**
     * Relacionamento com carteiras de fidelidade
     */
    public function carteirasFidelidade(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCarteira::class, 'empresa_id');
    }

    /**
     * Relacionamento com transações de cashback
     */
    public function transacoesCashback(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCashbackTransacao::class, 'empresa_id');
    }

    /**
     * Relacionamento com cupons
     */
    public function cupons(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCupom::class, 'empresa_id');
    }

    /**
     * Relacionamento com regras de cashback
     */
    public function regrasCashback(): HasMany
    {
        return $this->hasMany(\App\Models\Fidelidade\FidelidadeCashbackRegra::class, 'empresa_id');
    }

    /**
     * Scope para empresas ativas
     */
    public function scopeAtivas($query)
    {
        return $query->where('status', true);
    }
}
