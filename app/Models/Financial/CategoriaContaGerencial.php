<?php

namespace App\Models\Financial;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Model para Categoria de Conta Gerencial
 * 
 * Representa as categorias de negócio das contas gerenciais,
 * como Despesa Fixa, Custo Variável, Receita de Vendas, etc.
 */
class CategoriaContaGerencial extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'categorias_conta';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nome',
        'nome_completo',
        'descricao',
        'cor',
        'icone',
        'e_custo',
        'e_despesa',
        'e_receita',
        'ativo',
        'empresa_id',
        'sync_hash',
        'sync_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'e_custo' => 'boolean',
        'e_despesa' => 'boolean',
        'e_receita' => 'boolean',
        'ativo' => 'boolean',
        'sync_data' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com contas gerenciais
     */
    public function contasGerenciais(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'categoria_id');
    }

    /**
     * Relacionamento com empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Scope para categorias de custo
     */
    public function scopeCustos(Builder $query): Builder
    {
        return $query->where('e_custo', true);
    }

    /**
     * Scope para categorias de despesa
     */
    public function scopeDespesas(Builder $query): Builder
    {
        return $query->where('e_despesa', true);
    }

    /**
     * Scope para categorias de receita
     */
    public function scopeReceitas(Builder $query): Builder
    {
        return $query->where('e_receita', true);
    }

    /**
     * Scope para categorias ativas
     */
    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para categorias inativas
     */
    public function scopeInativas(Builder $query): Builder
    {
        return $query->where('ativo', false);
    }

    /**
     * Scope para categorias por empresa
     */
    public function scopePorEmpresa(Builder $query, int $empresaId): Builder
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Verifica se é categoria de custo
     */
    public function isCusto(): bool
    {
        return $this->e_custo;
    }

    /**
     * Verifica se é categoria de despesa
     */
    public function isDespesa(): bool
    {
        return $this->e_despesa;
    }

    /**
     * Verifica se é categoria de receita
     */
    public function isReceita(): bool
    {
        return $this->e_receita;
    }

    /**
     * Verifica se está ativa
     */
    public function isAtiva(): bool
    {
        return $this->ativo;
    }

    /**
     * Retorna o tipo principal da categoria
     */
    public function getTipo(): string
    {
        if ($this->e_custo) return 'custo';
        if ($this->e_despesa) return 'despesa';
        if ($this->e_receita) return 'receita';
        
        return 'indefinido';
    }

    /**
     * Retorna a cor com fallback padrão
     */
    public function getCorAttribute($value): string
    {
        if ($value) return $value;
        
        // Cores padrão por tipo
        if ($this->e_custo) return '#dc3545';     // Vermelho
        if ($this->e_despesa) return '#fd7e14';   // Laranja  
        if ($this->e_receita) return '#28a745';   // Verde
        
        return '#007bff'; // Azul padrão
    }

    /**
     * Conta o número de contas gerenciais vinculadas
     */
    public function getNumeroContasAttribute(): int
    {
        return $this->contasGerenciais()->count();
    }

    /**
     * Retorna o nome de exibição (nome_completo ou nome)
     */
    public function getNomeExibicaoAttribute(): string
    {
        return $this->nome_completo ?: $this->nome;
    }
}