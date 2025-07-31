<?php

namespace App\Models\Fidelidade;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramaFidelidade extends Model
{
    protected $table = 'programas_fidelidade';

    protected $fillable = [
        'business_id',
        'nome',
        'descricao',
        'pontos_por_real',
        'valor_ponto',
        'ativo',
        'data_inicio',
        'data_fim',
        'regras',
        'configuracoes'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'regras' => 'array',
        'configuracoes' => 'array',
        'pontos_por_real' => 'decimal:2',
        'valor_ponto' => 'decimal:4'
    ];

    /**
     * Relacionamento com Business
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Business\Business::class);
    }

    /**
     * Relacionamento com CartoesFidelidade
     */
    public function cartoes(): HasMany
    {
        return $this->hasMany(CartaoFidelidade::class);
    }

    /**
     * Relacionamento com TransacoesPontos
     */
    public function transacoesPontos(): HasMany
    {
        return $this->hasMany(TransacaoPontos::class);
    }

    /**
     * Scope para programas ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para programas vigentes
     */
    public function scopeVigentes($query)
    {
        $hoje = now()->toDateString();
        return $query->where(function ($q) use ($hoje) {
            $q->whereNull('data_inicio')
                ->orWhere('data_inicio', '<=', $hoje);
        })->where(function ($q) use ($hoje) {
            $q->whereNull('data_fim')
                ->orWhere('data_fim', '>=', $hoje);
        });
    }

    /**
     * Calcular pontos baseado no valor
     */
    public function calcularPontos(float $valor): int
    {
        return (int) floor($valor * $this->pontos_por_real);
    }

    /**
     * Calcular valor dos pontos
     */
    public function calcularValorPontos(int $pontos): float
    {
        return $pontos * $this->valor_ponto;
    }
}
