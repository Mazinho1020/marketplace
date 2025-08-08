<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AfiPlanPlanos extends Model
{
    use HasFactory;

    protected $table = 'afi_plan_planos';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'descricao',
        'preco_mensal',
        'preco_anual',
        'preco_vitalicio',
        'dias_trial',
        'recursos',
        'limites',
        'ativo'
    ];

    protected $casts = [
        'preco_mensal' => 'decimal:2',
        'preco_anual' => 'decimal:2',
        'preco_vitalicio' => 'decimal:2',
        'recursos' => 'array',
        'limites' => 'array',
        'ativo' => 'boolean',
        'dias_trial' => 'integer'
    ];

    /**
     * Relacionamento com assinaturas
     */
    public function assinaturas()
    {
        return $this->hasMany(AfiPlanAssinaturas::class, 'plano_id');
    }

    /**
     * Verificar se o plano tem um recurso específico
     */
    public function hasFeature(string $feature): bool
    {
        $recursos = $this->recursos ?? [];
        return isset($recursos[$feature]) && $recursos[$feature];
    }

    /**
     * Obter limite de um recurso
     */
    public function getLimit(string $resource): int
    {
        $limites = $this->limites ?? [];
        return $limites[$resource] ?? 0;
    }

    /**
     * Verificar se é um plano ilimitado para um recurso
     */
    public function isUnlimited(string $resource): bool
    {
        return $this->getLimit($resource) === -1;
    }

    /**
     * Scope para planos ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para planos globais (empresa_id = 0 ou 1)
     */
    public function scopeGlobal($query)
    {
        return $query->whereIn('empresa_id', [0, 1]);
    }

    /**
     * Calcular desconto anual (%)
     */
    public function getDescontoAnualAttribute(): float
    {
        if ($this->preco_mensal <= 0 || $this->preco_anual <= 0) {
            return 0;
        }

        $precoAnualCalculado = $this->preco_mensal * 12;
        $desconto = (($precoAnualCalculado - $this->preco_anual) / $precoAnualCalculado) * 100;

        return round($desconto, 1);
    }

    /**
     * Obter cor do plano baseado no código
     */
    public function getCorAttribute(): string
    {
        return match ($this->codigo) {
            'basico' => 'primary',
            'premium' => 'warning',
            'enterprise' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Verificar se é plano mais popular
     */
    public function getIsPopularAttribute(): bool
    {
        return $this->codigo === 'premium';
    }
}
