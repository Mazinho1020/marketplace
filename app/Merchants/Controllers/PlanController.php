<?php

namespace App\Merchants\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Merchants\Models\SubscriptionPlan;
use App\Merchants\Models\MerchantSubscription;
use App\Merchants\Services\PlanService;
use App\Core\Config\ConfigManager;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    private $planService;
    private $config;

    public function __construct(PlanService $planService, ConfigManager $config)
    {
        $this->planService = $planService;
        $this->config = $config;
    }

    /**
     * Exibir página de planos
     */
    public function index(Request $request)
    {
        $plans = SubscriptionPlan::active()
            ->ordered()
            ->get();

        $merchant = $this->getCurrentMerchant();
        $currentSubscription = $merchant?->activeSubscription;

        // Configurações públicas
        $publicConfig = $this->config->getPublicConfig();

        if ($request->expectsJson()) {
            return response()->json([
                'plans' => $plans->map(function ($plan) use ($currentSubscription) {
                    return [
                        'id' => $plan->id,
                        'code' => $plan->code,
                        'name' => $plan->name,
                        'description' => $plan->description,
                        'price_monthly' => $plan->price_monthly,
                        'price_yearly' => $plan->price_yearly,
                        'price_lifetime' => $plan->price_lifetime,
                        'formatted_price_monthly' => $plan->formatted_price_monthly,
                        'formatted_price_yearly' => $plan->formatted_price_yearly,
                        'features' => $plan->getFormattedFeatures(),
                        'limits' => $plan->getFormattedLimits(),
                        'trial_days' => $plan->trial_days,
                        'annual_discount_percentage' => $plan->getAnnualDiscountPercentage(),
                        'annual_savings' => $plan->getAnnualSavings(),
                        'is_current' => $currentSubscription?->plan_id === $plan->id,
                        'can_upgrade_to' => $currentSubscription ? $plan->isUpgradeFrom($currentSubscription->plan) : true,
                        'can_downgrade_to' => $currentSubscription ? $plan->isDowngradeFrom($currentSubscription->plan) : false,
                        'is_popular' => $plan->is_popular
                    ];
                }),
                'current_subscription' => $currentSubscription ? [
                    'plan_name' => $currentSubscription->plan_name,
                    'billing_cycle' => $currentSubscription->billing_cycle,
                    'amount' => $currentSubscription->amount,
                    'status' => $currentSubscription->status,
                    'expires_at' => $currentSubscription->expires_at?->toISOString(),
                    'days_to_expiration' => $currentSubscription->days_to_expiration
                ] : null,
                'config' => $publicConfig
            ]);
        }

        return view('merchants.plans.index', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'merchant' => $merchant,
            'config' => $publicConfig
        ]);
    }

    /**
     * Página de upgrade
     */
    public function upgrade(Request $request)
    {
        $merchant = $this->getCurrentMerchant();
        $currentSubscription = $merchant?->activeSubscription;

        if (!$currentSubscription) {
            return redirect()->route('merchant.plans.index')
                ->with('info', 'Selecione um plano para começar.');
        }

        // Planos disponíveis para upgrade
        $availablePlans = SubscriptionPlan::active()
            ->where('id', '!=', $currentSubscription->plan_id)
            ->get()
            ->filter(function ($plan) use ($currentSubscription) {
                return $plan->isUpgradeFrom($currentSubscription->plan);
            });

        // Informações sobre o bloqueio
        $blockedFeature = $request->session()->get('blocked_feature');
        $requiredPlan = $request->session()->get('required_plan');

        if ($request->expectsJson()) {
            return response()->json([
                'current_plan' => [
                    'name' => $currentSubscription->plan_name,
                    'features' => $currentSubscription->features,
                    'amount' => $currentSubscription->amount,
                    'billing_cycle' => $currentSubscription->billing_cycle
                ],
                'available_plans' => $availablePlans->values(),
                'blocked_feature' => $blockedFeature,
                'required_plan' => $requiredPlan,
                'usage_info' => $merchant->getUsageInfo()
            ]);
        }

        return view('merchants.plans.upgrade', [
            'currentSubscription' => $currentSubscription,
            'availablePlans' => $availablePlans,
            'merchant' => $merchant,
            'blockedFeature' => $blockedFeature,
            'requiredPlan' => $requiredPlan,
            'usageInfo' => $merchant->getUsageInfo()
        ]);
    }

    /**
     * Comparar planos
     */
    public function compare(Request $request)
    {
        $planIds = $request->input('plans', []);

        if (empty($planIds) || count($planIds) < 2) {
            return redirect()->route('merchant.plans.index')
                ->with('error', 'Selecione pelo menos 2 planos para comparar.');
        }

        $plans = SubscriptionPlan::active()
            ->whereIn('id', $planIds)
            ->get();

        if ($plans->count() < 2) {
            return redirect()->route('merchant.plans.index')
                ->with('error', 'Planos selecionados não encontrados.');
        }

        // Preparar dados de comparação
        $comparison = $this->planService->comparePlans($plans);

        if ($request->expectsJson()) {
            return response()->json($comparison);
        }

        return view('merchants.plans.compare', [
            'plans' => $plans,
            'comparison' => $comparison
        ]);
    }

    /**
     * Detalhes de um plano específico
     */
    public function show(SubscriptionPlan $plan, Request $request)
    {
        if (!$plan->is_active) {
            abort(404);
        }

        $merchant = $this->getCurrentMerchant();
        $currentSubscription = $merchant?->activeSubscription;

        // Calcular valores para diferentes ciclos
        $pricing = [
            'monthly' => [
                'price' => $plan->price_monthly,
                'formatted' => $plan->formatted_price_monthly,
                'per_year' => $plan->price_monthly * 12
            ],
            'yearly' => [
                'price' => $plan->price_yearly,
                'formatted' => $plan->formatted_price_yearly,
                'savings' => $plan->getAnnualSavings(),
                'discount_percentage' => $plan->getAnnualDiscountPercentage()
            ],
            'lifetime' => [
                'price' => $plan->price_lifetime,
                'formatted' => $plan->formatted_price_lifetime
            ]
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'plan' => $plan,
                'pricing' => $pricing,
                'features' => $plan->getFormattedFeatures(),
                'limits' => $plan->getFormattedLimits(),
                'can_subscribe' => !$currentSubscription || $plan->id !== $currentSubscription->plan_id,
                'is_upgrade' => $currentSubscription ? $plan->isUpgradeFrom($currentSubscription->plan) : true,
                'is_downgrade' => $currentSubscription ? $plan->isDowngradeFrom($currentSubscription->plan) : false
            ]);
        }

        return view('merchants.plans.show', [
            'plan' => $plan,
            'pricing' => $pricing,
            'currentSubscription' => $currentSubscription,
            'merchant' => $merchant
        ]);
    }

    /**
     * Calcular preço para upgrade
     */
    public function calculateUpgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime'
        ]);

        $merchant = $this->getCurrentMerchant();
        $currentSubscription = $merchant?->activeSubscription;

        if (!$currentSubscription) {
            return response()->json([
                'error' => 'Nenhuma assinatura ativa encontrada'
            ], 400);
        }

        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);

        // Calcular custos
        $calculation = $this->planService->calculateUpgradeCost(
            $currentSubscription,
            $newPlan,
            $request->billing_cycle
        );

        return response()->json($calculation);
    }

    /**
     * Obter merchant atual
     */
    private function getCurrentMerchant()
    {
        if (!Auth::check()) {
            return null;
        }

        $user = Auth::user();

        // Adaptar conforme sua estrutura
        return $user->merchant ?? \App\Merchants\Models\Merchant::where('email', $user->email)->first();
    }
}
