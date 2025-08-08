<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AfiPlanGateways extends Model
{
    use HasFactory;

    protected $table = 'afi_plan_gateways';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nome',
        'provedor',
        'ambiente',
        'ativo',
        'credenciais',
        'configuracoes',
        'url_webhook'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'credenciais' => 'array',
        'configuracoes' => 'array'
    ];

    /**
     * Ambientes possíveis
     */
    const AMBIENTE_SANDBOX = 'sandbox';
    const AMBIENTE_PRODUCAO = 'producao';

    /**
     * Relacionamento com transações
     */
    public function transacoes()
    {
        return $this->hasMany(AfiPlanTransacoes::class, 'gateway_id');
    }

    /**
     * Scope para gateways ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para gateways globais
     */
    public function scopeGlobal($query)
    {
        return $query->whereIn('empresa_id', [0, 1]);
    }

    /**
     * Verificar se suporta um método de pagamento
     */
    public function supportsMethod(string $method): bool
    {
        $configuracoes = $this->configuracoes ?? [];
        $supportedMethods = $configuracoes['supported_methods'] ?? [];

        return in_array($method, $supportedMethods);
    }

    /**
     * Obter taxa de um método de pagamento
     */
    public function getFee(string $method): float
    {
        $configuracoes = $this->configuracoes ?? [];
        $fees = $configuracoes['fees'] ?? [];

        return $fees[$method] ?? 0;
    }
}
