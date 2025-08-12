<?php

namespace App\Models\Financial;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model para Conta Gerencial
 * 
 * Representa uma conta do plano de contas gerencial,
 * com relacionamento direto para categoria de negócio.
 */
class ContaGerencial extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'conta_gerencial';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nome',
        'descricao', 
        'usuario_id',
        'empresa_id',
        'classificacao_dre_id',
        'tipo_id',
        'categoria_id',
        'natureza_conta',
        'sync_hash',
        'sync_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com categoria de conta
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaContaGerencial::class, 'categoria_id');
    }

    /**
     * Relacionamento com empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    /**
     * Scope para contas de custo
     */
    public function scopeCustos(Builder $query): Builder
    {
        return $query->whereHas('categoria', function ($q) {
            $q->where('e_custo', true);
        });
    }

    /**
     * Scope para contas de despesa
     */
    public function scopeDespesas(Builder $query): Builder
    {
        return $query->whereHas('categoria', function ($q) {
            $q->where('e_despesa', true);
        });
    }

    /**
     * Scope para contas de receita
     */
    public function scopeReceitas(Builder $query): Builder
    {
        return $query->whereHas('categoria', function ($q) {
            $q->where('e_receita', true);
        });
    }

    /**
     * Scope para contas ativas
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->whereHas('categoria', function ($q) {
            $q->where('ativo', true);
        });
    }

    /**
     * Scope para contas de débito
     */
    public function scopeDebito(Builder $query): Builder
    {
        return $query->where('natureza_conta', 'debito');
    }

    /**
     * Scope para contas de crédito
     */
    public function scopeCredito(Builder $query): Builder
    {
        return $query->where('natureza_conta', 'credito');
    }

    /**
     * Verifica se a conta é de custo
     */
    public function isCusto(): bool
    {
        return $this->categoria?->e_custo ?? false;
    }

    /**
     * Verifica se a conta é de despesa
     */
    public function isDespesa(): bool
    {
        return $this->categoria?->e_despesa ?? false;
    }

    /**
     * Verifica se a conta é de receita
     */
    public function isReceita(): bool
    {
        return $this->categoria?->e_receita ?? false;
    }

    /**
     * Verifica se a conta é de débito
     */
    public function isDebito(): bool
    {
        return $this->natureza_conta === 'debito';
    }

    /**
     * Verifica se a conta é de crédito
     */
    public function isCredito(): bool
    {
        return $this->natureza_conta === 'credito';
    }

    /**
     * Retorna a cor da categoria
     */
    public function getCor(): string
    {
        return $this->categoria?->cor ?? '#007bff';
    }

    /**
     * Retorna o ícone da categoria
     */
    public function getIcone(): ?string
    {
        return $this->categoria?->icone;
    }

    /**
     * Retorna o nome completo da categoria
     */
    public function getNomeCategoria(): string
    {
        return $this->categoria?->nome_completo ?? 'Sem categoria';
    }
}