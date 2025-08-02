<?php

namespace App\Merchants\Services;

use App\Merchants\Models\SubscriptionPlan;
use App\Merchants\Models\MerchantSubscription;
use App\Merchants\Models\Merchant;
use App\Core\Config\ConfigManager;
use App\Core\Cache\RedisCacheManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlanService
{
    private $config;
    private $cache;

    public function __construct(ConfigManager $config, RedisCacheManager $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Verificar se um merchant tem uma feature específica
     */
    public function hasFeature(int $merchantId, string $feature): bool
    {
        // Buscar do cache primeiro
        $features = $this->cache->getMerchantFeatures($merchantId);

        if ($features === null) {
            $merchant = Merchant::find($merchantId);
            $features = $merchant ? $merchant->getActiveFeatures() : [];
        }

        return in_array($feature, $features);
    }

    /**
     * Obter features ativas de um plano
     */
    public function getPlanFeatures(string $planCode): array
    {
        $cacheKey = "plan_features_{$planCode}";

        return $this->cache->remember($cacheKey, function () use ($planCode) {
            $plan = SubscriptionPlan::where('code', $planCode)->first();
            return $plan ? $plan->features : [];
        });
    }

    /**
     * Comparar múltiplos planos
     */
    public function comparePlans(Collection $plans): array
    {
        $comparison = [
            'plans' => [],
            'features_matrix' => [],
            'limits_matrix' => []
        ];

        // Coletar todas as features e limites únicos
        $allFeatures = collect();
        $allLimits = collect();

        foreach ($plans as $plan) {
            $allFeatures = $allFeatures->merge($plan->features ?? []);
            $allLimits = $allLimits->merge(array_keys($plan->limits ?? []));
        }

        $allFeatures = $allFeatures->unique()->sort()->values();
        $allLimits = $allLimits->unique()->sort()->values();

        // Montar matriz de comparação
        foreach ($plans as $plan) {
            $comparison['plans'][] = [
                'id' => $plan->id,
                'name' => $plan->name,
                'code' => $plan->code,
                'price_monthly' => $plan->price_monthly,
                'price_yearly' => $plan->price_yearly,
                'annual_savings' => $plan->getAnnualSavings(),
                'is_popular' => $plan->is_popular
            ];

            // Features matrix
            $featureMatrix = [];
            foreach ($allFeatures as $feature) {
                $featureMatrix[$feature] = in_array($feature, $plan->features ?? []);
            }
            $comparison['features_matrix'][] = $featureMatrix;

            // Limits matrix
            $limitMatrix = [];
            foreach ($allLimits as $limit) {
                $limitMatrix[$limit] = $plan->limits[$limit] ?? 0;
            }
            $comparison['limits_matrix'][] = $limitMatrix;
        }

        $comparison['all_features'] = $allFeatures;
        $comparison['all_limits'] = $allLimits;

        return $comparison;
    }

    /**
     * Calcular custo de upgrade
     */
    public function calculateUpgradeCost(
        MerchantSubscription $currentSubscription,
        SubscriptionPlan $newPlan,
        string $billingCycle
    ): array {

        $newAmount = $newPlan->getPriceForCycle($billingCycle);
        $proRatedCredit = 0;
        $immediateCharge = $newAmount;

        // Calcular crédito proporcional se aplicável
        if ($currentSubscription->expires_at && $currentSubscription->expires_at->isFuture()) {
            $daysRemaining = now()->diffInDays($currentSubscription->expires_at);
            $totalDays = $this->getTotalDaysForCycle($currentSubscription->billing_cycle);

            if ($totalDays > 0) {
                $proRatedCredit = ($currentSubscription->amount / $totalDays) * $daysRemaining;
                $immediateCharge = max(0, $newAmount - $proRatedCredit);
            }
        }

        // Calcular próxima data de cobrança
        $nextBillingDate = $this->calculateNextBillingDate($billingCycle);

        return [
            'current_plan' => [
                'name' => $currentSubscription->plan_name,
                'amount' => $currentSubscription->amount,
                'billing_cycle' => $currentSubscription->billing_cycle,
                'expires_at' => $currentSubscription->expires_at?->toISOString()
            ],
            'new_plan' => [
                'name' => $newPlan->name,
                'amount' => $newAmount,
                'billing_cycle' => $billingCycle
            ],
            'cost_breakdown' => [
                'new_plan_price' => $newAmount,
                'pro_rated_credit' => $proRatedCredit,
                'immediate_charge' => $immediateCharge,
                'next_billing_date' => $nextBillingDate->toISOString(),
                'savings' => $billingCycle === 'yearly' ? $newPlan->getAnnualSavings() : 0
            ],
            'features_comparison' => $newPlan->compareFeaturesWith($currentSubscription->plan)
        ];
    }

    /**
     * Verificar eligibilidade para trial
     */
    public function isEligibleForTrial(Merchant $merchant, SubscriptionPlan $plan): bool
    {
        // Verificar se já usou trial antes
        $hasUsedTrial = MerchantSubscription::where('merchant_id', $merchant->id)
            ->where('status', 'trial')
            ->exists();

        return !$hasUsedTrial && $plan->trial_days > 0;
    }

    /**
     * Obter recomendação de plano baseado no uso
     */
    public function getRecommendedPlan(Merchant $merchant): ?SubscriptionPlan
    {
        $usage = $merchant->getUsageInfo();
        $currentPlan = $merchant->activeSubscription?->plan;

        // Lógica de recomendação baseada no uso
        if ($usage['users']['percentage'] > 80 || $usage['transactions']['percentage'] > 80) {
            // Recomendar upgrade se uso > 80%
            return $this->getNextPlanUp($currentPlan);
        }

        if ($usage['users']['percentage'] < 30 && $usage['transactions']['percentage'] < 30) {
            // Recomendar downgrade se uso < 30%
            return $this->getNextPlanDown($currentPlan);
        }

        return null; // Plano atual é adequado
    }

    /**
     * Obter próximo plano superior
     */
    private function getNextPlanUp(?SubscriptionPlan $currentPlan): ?SubscriptionPlan
    {
        if (!$currentPlan) {
            return SubscriptionPlan::where('code', 'basic')->first();
        }

        $hierarchy = ['basic' => 'premium', 'premium' => 'enterprise'];
        $nextCode = $hierarchy[$currentPlan->code] ?? null;

        return $nextCode ? SubscriptionPlan::where('code', $nextCode)->first() : null;
    }

    /**
     * Obter próximo plano inferior
     */
    private function getNextPlanDown(?SubscriptionPlan $currentPlan): ?SubscriptionPlan
    {
        if (!$currentPlan) {
            return null;
        }

        $hierarchy = ['enterprise' => 'premium', 'premium' => 'basic'];
        $nextCode = $hierarchy[$currentPlan->code] ?? null;

        return $nextCode ? SubscriptionPlan::where('code', $nextCode)->first() : null;
    }

    /**
     * Verificar limites de uso
     */
    public function checkUsageLimits(Merchant $merchant): array
    {
        $limits = $merchant->getActiveLimits();
        $usage = $merchant->getUsageInfo();
        $warnings = [];

        foreach ($usage as $type => $data) {
            if ($data['percentage'] > 90) {
                $warnings[] = [
                    'type' => $type,
                    'message' => "Você está usando {$data['percentage']}% do seu limite de {$type}",
                    'current' => $data['current'],
                    'limit' => $data['limit'],
                    'severity' => 'critical'
                ];
            } elseif ($data['percentage'] > 75) {
                $warnings[] = [
                    'type' => $type,
                    'message' => "Você está próximo do limite de {$type} ({$data['percentage']}%)",
                    'current' => $data['current'],
                    'limit' => $data['limit'],
                    'severity' => 'warning'
                ];
            }
        }

        return $warnings;
    }

    /**
     * Calcular ROI de upgrade
     */
    public function calculateUpgradeROI(
        MerchantSubscription $currentSubscription,
        SubscriptionPlan $newPlan,
        array $projectedGrowth = []
    ): array {

        $monthlyIncrease = $newPlan->price_monthly - $currentSubscription->amount;
        $newFeatures = array_diff($newPlan->features, $currentSubscription->features);

        // Projeções baseadas nas novas features
        $projectedRevenue = 0;
        if (in_array('pdv', $newFeatures)) {
            $projectedRevenue += $projectedGrowth['pdv_revenue'] ?? 500; // R$ 500/mês estimado
        }
        if (in_array('delivery', $newFeatures)) {
            $projectedRevenue += $projectedGrowth['delivery_revenue'] ?? 300; // R$ 300/mês estimado
        }

        $roi = $projectedRevenue > 0 ? (($projectedRevenue - $monthlyIncrease) / $monthlyIncrease) * 100 : 0;

        return [
            'monthly_cost_increase' => $monthlyIncrease,
            'projected_monthly_revenue' => $projectedRevenue,
            'estimated_roi_percentage' => round($roi, 2),
            'payback_period_months' => $projectedRevenue > 0 ? ceil($monthlyIncrease / ($projectedRevenue - $monthlyIncrease)) : null,
            'new_features' => $newFeatures
        ];
    }

    /**
     * Obter estatísticas de planos
     */
    public function getPlanStatistics(): array
    {
        return $this->cache->remember('plan_statistics', function () {
            return [
                'total_subscriptions' => MerchantSubscription::where('status', 'active')->count(),
                'mrr_total' => $this->calculateTotalMRR(),
                'plan_distribution' => $this->getPlanDistribution(),
                'churn_rate' => $this->calculateChurnRate(),
                'upgrade_rate' => $this->calculateUpgradeRate(),
                'trial_conversion' => $this->calculateTrialConversion()
            ];
        }, 3600); // 1 hora
    }

    /**
     * Métodos auxiliares privados
     */
    private function getTotalDaysForCycle(string $cycle): int
    {
        return match ($cycle) {
            'monthly' => 30,
            'yearly' => 365,
            'lifetime' => 0,
            default => 30
        };
    }

    private function calculateNextBillingDate(string $cycle): \Carbon\Carbon
    {
        return match ($cycle) {
            'monthly' => now()->addMonth(),
            'yearly' => now()->addYear(),
            'lifetime' => now()->addYears(100), // Arbitrário para lifetime
            default => now()->addMonth()
        };
    }

    private function calculateTotalMRR(): float
    {
        $result = DB::selectOne("
            SELECT 
                (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'active' AND billing_cycle = 'monthly') +
                (SELECT COALESCE(SUM(amount)/12, 0) FROM merchant_subscriptions WHERE status = 'active' AND billing_cycle = 'yearly') as mrr
        ");

        return $result->mrr ?? 0;
    }

    private function getPlanDistribution(): array
    {
        return DB::table('merchant_subscriptions')
            ->select('plan_code', DB::raw('COUNT(*) as count'))
            ->where('status', 'active')
            ->groupBy('plan_code')
            ->pluck('count', 'plan_code')
            ->toArray();
    }

    private function calculateChurnRate(): float
    {
        $result = DB::selectOne("
            SELECT 
                ROUND(
                    COALESCE(
                        (COUNT(CASE WHEN status = 'cancelled' AND cancelled_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) * 100.0) / 
                        NULLIF(COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END), 0),
                        0
                    ),
                    2
                ) as churn_rate
            FROM merchant_subscriptions
        ");

        return $result->churn_rate ?? 0;
    }

    private function calculateUpgradeRate(): float
    {
        // Implementar lógica de cálculo de upgrade rate
        return 0; // Placeholder
    }

    private function calculateTrialConversion(): float
    {
        $result = DB::selectOne("
            SELECT 
                ROUND(
                    COALESCE(
                        (COUNT(CASE WHEN status = 'active' AND trial_ends_at IS NOT NULL THEN 1 END) * 100.0) / 
                        NULLIF(COUNT(CASE WHEN trial_ends_at IS NOT NULL THEN 1 END), 0),
                        0
                    ),
                    2
                ) as conversion_rate
            FROM merchant_subscriptions
        ");

        return $result->conversion_rate ?? 0;
    }
}
