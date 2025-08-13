<?php

namespace App\Models\Financial;

use App\Models\Financial\BaseFinancialModel;
use App\Traits\HasSync;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaContaGerencial extends BaseFinancialModel
{
    use HasSync, HasCompany;

    protected $table = 'categorias_conta';

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
        'sync_status',
        'sync_hash',
        'sync_data',
    ];

    protected $casts = [
        'e_custo' => 'boolean',
        'e_despesa' => 'boolean',
        'e_receita' => 'boolean',
        'ativo' => 'boolean',
        'sync_data' => 'datetime',
    ];

    /**
     * Relacionamentos
     */
    public function contasGerenciais(): HasMany
    {
        return $this->hasMany(ContaGerencial::class, 'categoria_id');
    }

    /**
     * Scopes por tipo
     */
    public function scopeCustos($query)
    {
        return $query->where('e_custo', true);
    }

    public function scopeDespesas($query)
    {
        return $query->where('e_despesa', true);
    }

    public function scopeReceitas($query)
    {
        return $query->where('e_receita', true);
    }

    /**
     * Métodos auxiliares
     */
    public function getTipoAttribute(): string
    {
        if ($this->e_custo) return 'Custo';
        if ($this->e_despesa) return 'Despesa';
        if ($this->e_receita) return 'Receita';
        return 'Outros';
    }

    public function getTiposAttribute(): array
    {
        $tipos = [];
        if ($this->e_custo) $tipos[] = 'Custo';
        if ($this->e_despesa) $tipos[] = 'Despesa';
        if ($this->e_receita) $tipos[] = 'Receita';
        return $tipos;
    }

    public function getCorFormatadaAttribute(): string
    {
        return $this->cor ?: '#007bff';
    }

    public function getIconeFormatadoAttribute(): string
    {
        return $this->icone ?: 'circle';
    }

    /**
     * Verifica se é de um tipo específico
     */
    public function isCusto(): bool
    {
        return $this->e_custo;
    }

    public function isDespesa(): bool
    {
        return $this->e_despesa;
    }

    public function isReceita(): bool
    {
        return $this->e_receita;
    }
}
