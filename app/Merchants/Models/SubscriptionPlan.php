<?php

namespace App\Merchants\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Core\Config\ConfigManager;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'price_monthly',
        'price_yearly',
        'price_lifetime',
        'features',
        'limits',
        'trial_days',
        'is_active',
        'sort_order',
        'metadata'
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'price_lifetime' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'trial_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamentos
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(MerchantSubscription::class, 'plan_id');
    }

    /**
     * Verificar se o plano tem uma feature específica
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Obter limite específico
     */
    public function getLimit(string $limitType): ?int
    {
        return $this->limits[$limitType] ?? null;
    }

    /**
     * Verificar se é plano gratuito
     */
    public function isFree(): bool
    {
        return $this->price_monthly == 0 && $this->price_yearly == 0;
    }

    /**
     * Verificar se tem desconto anual
     */
    public function hasAnnualDiscount(): bool
    {
        return $this->price_yearly < ($this->price_monthly * 12);
    }

    /**
     * Calcular desconto anual em percentual
     */
    public function getAnnualDiscountPercentage(): float
    {
        if (!$this->hasAnnualDiscount()) {
            return 0;
        }

        $monthlyYearly = $this->price_monthly * 12;
        $savings = $monthlyYearly - $this->price_yearly;

        return round(($savings / $monthlyYearly) * 100, 2);
    }

    /**
     * Calcular economia anual em valor
     */
    public function getAnnualSavings(): float
    {
        if (!$this->hasAnnualDiscount()) {
            return 0;
        }

        return ($this->price_monthly * 12) - $this->price_yearly;
    }

    /**
     * Obter preço baseado no ciclo de cobrança
     */
    public function getPriceForCycle(string $cycle): float
    {
        return match ($cycle) {
            'monthly' => $this->price_monthly,
            'yearly' => $this->price_yearly,
            'lifetime' => $this->price_lifetime,
            default => $this->price_monthly
        };
    }

    /**
     * Verificar se é upgrade em relação a outro plano
     */
    public function isUpgradeFrom(SubscriptionPlan $otherPlan): bool
    {
        // Ordem de planos: basic < premium < enterprise
        $hierarchy = [
            'basic' => 1,
            'premium' => 2,
            'enterprise' => 3
        ];

        $thisLevel = $hierarchy[$this->code] ?? 0;
        $otherLevel = $hierarchy[$otherPlan->code] ?? 0;

        return $thisLevel > $otherLevel;
    }

    /**
     * Verificar se é downgrade em relação a outro plano
     */
    public function isDowngradeFrom(SubscriptionPlan $otherPlan): bool
    {
        return $otherPlan->isUpgradeFrom($this);
    }

    /**
     * Comparar features com outro plano
     */
    public function compareFeaturesWith(SubscriptionPlan $otherPlan): array
    {
        $myFeatures = $this->features ?? [];
        $otherFeatures = $otherPlan->features ?? [];

        return [
            'added' => array_diff($myFeatures, $otherFeatures),
            'removed' => array_diff($otherFeatures, $myFeatures),
            'common' => array_intersect($myFeatures, $otherFeatures)
        ];
    }

    /**
     * Obter features formatadas para exibição
     */
    public function getFormattedFeatures(): array
    {
        $featureNames = [
            'financeiro' => 'Gestão Financeira',
            'pdv' => 'PDV Completo',
            'delivery' => 'Sistema de Delivery',
            'relatorios_basicos' => 'Relatórios Básicos',
            'relatorios_avancados' => 'Relatórios Avançados',
            'multi_empresa' => 'Multi-empresa',
            'api' => 'API Completa',
            'backup_automatico' => 'Backup Automático',
            'suporte_prioritario' => 'Suporte Prioritário',
            'suporte_24h' => 'Suporte 24h'
        ];

        return array_map(function ($feature) use ($featureNames) {
            return $featureNames[$feature] ?? ucfirst(str_replace('_', ' ', $feature));
        }, $this->features ?? []);
    }

    /**
     * Obter limites formatados para exibição
     */
    public function getFormattedLimits(): array
    {
        $limits = $this->limits ?? [];
        $formatted = [];

        if (isset($limits['users'])) {
            $formatted['Usuários'] = $limits['users'] == -1 ? 'Ilimitado' : $limits['users'];
        }

        if (isset($limits['companies'])) {
            $formatted['Empresas'] = $limits['companies'] == -1 ? 'Ilimitado' : $limits['companies'];
        }

        if (isset($limits['transactions_per_month'])) {
            $formatted['Transações/mês'] = $limits['transactions_per_month'] == -1
                ? 'Ilimitado'
                : number_format($limits['transactions_per_month']);
        }

        return $formatted;
    }

    /**
     * Criar plano padrão baseado em configurações
     */
    public static function createFromConfig(string $planCode, ConfigManager $config): self
    {
        $planData = [
            'basic' => [
                'name' => 'Plano Básico',
                'description' => 'Ideal para pequenas empresas',
                'features' => ['financeiro', 'relatorios_basicos', 'backup_automatico'],
                'limits' => [
                    'users' => $config->get('comerciantes_limite_usuarios_basic', 1, 3),
                    'companies' => 1,
                    'transactions_per_month' => 1000
                ]
            ],
            'premium' => [
                'name' => 'Plano Premium',
                'description' => 'Solução completa para crescimento',
                'features' => ['financeiro', 'pdv', 'delivery', 'relatorios_avancados', 'backup_automatico'],
                'limits' => [
                    'users' => $config->get('comerciantes_limite_usuarios_premium', 1, 10),
                    'companies' => 1,
                    'transactions_per_month' => 10000
                ]
            ],
            'enterprise' => [
                'name' => 'Plano Enterprise',
                'description' => 'Para grandes operações',
                'features' => ['financeiro', 'pdv', 'delivery', 'multi_empresa', 'relatorios_avancados', 'api', 'backup_automatico', 'suporte_24h'],
                'limits' => [
                    'users' => 50,
                    'companies' => 5,
                    'transactions_per_month' => 50000
                ]
            ]
        ];

        $data = $planData[$planCode] ?? $planData['basic'];

        return self::create([
            'code' => $planCode,
            'name' => $data['name'],
            'description' => $data['description'],
            'price_monthly' => $config->get("planos_{$planCode}_mensal", 1, 97.00),
            'price_yearly' => $config->get("planos_{$planCode}_anual", 1, 970.00),
            'price_lifetime' => $config->get("planos_{$planCode}_vitalicio", 1, 1997.00),
            'features' => $data['features'],
            'limits' => $data['limits'],
            'trial_days' => $config->get('planos_dias_trial', 1, 7),
            'is_active' => true,
            'sort_order' => ['basic' => 1, 'premium' => 2, 'enterprise' => 3][$planCode] ?? 1
        ]);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeForPublic($query)
    {
        return $query->where('is_active', true)
            ->where('code', '!=', 'free')
            ->orderBy('sort_order');
    }

    /**
     * Atributos calculados
     */
    public function getHasTrialAttribute(): bool
    {
        return $this->trial_days > 0;
    }

    public function getIsPopularAttribute(): bool
    {
        // Marca como popular baseado em metadata ou lógica de negócio
        return ($this->metadata['popular'] ?? false) || $this->code === 'premium';
    }

    public function getFormattedPriceMonthlyAttribute(): string
    {
        return 'R$ ' . number_format($this->price_monthly, 2, ',', '.');
    }

    public function getFormattedPriceYearlyAttribute(): string
    {
        return 'R$ ' . number_format($this->price_yearly, 2, ',', '.');
    }

    public function getFormattedPriceLifetimeAttribute(): string
    {
        return 'R$ ' . number_format($this->price_lifetime, 2, ',', '.');
    }
}
