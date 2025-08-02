<?php

namespace App\Merchants\Controllers;

use App\Http\Controllers\Controller;
use App\Merchants\Models\Merchant;
use App\Merchants\Models\SubscriptionPlan;
use App\Merchants\Models\MerchantSubscription;
use App\Merchants\Services\PlanService;
use App\Core\Config\ConfigManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    private $planService;
    private $config;

    public function __construct(PlanService $planService, ConfigManager $config)
    {
        $this->planService = $planService;
        $this->config = $config;
    }

    /**
     * Mostrar subscription atual do merchant
     */
    public function show(): JsonResponse
    {
        try {
            $merchant = Auth::user()->merchant;
            if (!$merchant) {
                return response()->json(['error' => 'Merchant não encontrado'], 404);
            }

            $subscription = $merchant->activeSubscription;
            $usage = $merchant->getUsageInfo();
            $warnings = $this->planService->checkUsageLimits($merchant);
            $recommendation = $this->planService->getRecommendedPlan($merchant);

            return response()->json([
                'subscription' => $subscription ? [
                    'id' => $subscription->id,
                    'plan_name' => $subscription->plan_name,
                    'plan_code' => $subscription->plan_code,
                    'status' => $subscription->status,
                    'amount' => $subscription->amount,
                    'billing_cycle' => $subscription->billing_cycle,
                    'started_at' => $subscription->started_at?->toISOString(),
                    'expires_at' => $subscription->expires_at?->toISOString(),
                    'auto_renew' => $subscription->auto_renew,
                    'is_trial' => $subscription->is_trial,
                    'trial_ends_at' => $subscription->trial_ends_at?->toISOString(),
                    'features' => $subscription->features,
                    'limits' => $subscription->limits
                ] : null,
                'usage' => $usage,
                'warnings' => $warnings,
                'recommendation' => $recommendation ? [
                    'plan' => $recommendation->toArray(),
                    'reason' => $this->getRecommendationReason($merchant, $recommendation)
                ] : null,
                'can_upgrade' => $this->canUpgrade($merchant),
                'can_downgrade' => $this->canDowngrade($merchant)
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar subscription: ' . $e->getMessage());
            return response()->json(['error' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Iniciar subscription (para novos merchants)
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_code' => 'required|string|exists:subscription_plans,code',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'payment_method' => 'required|string',
            'use_trial' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $merchant = Auth::user()->merchant;
            if (!$merchant) {
                return response()->json(['error' => 'Merchant não encontrado'], 404);
            }

            // Verificar se já tem subscription ativa
            if ($merchant->activeSubscription) {
                return response()->json(['error' => 'Merchant já possui subscription ativa'], 400);
            }

            $plan = SubscriptionPlan::where('code', $request->plan_code)->first();
            $useTrial = $request->boolean('use_trial') && $this->planService->isEligibleForTrial($merchant, $plan);

            DB::beginTransaction();

            $subscription = new MerchantSubscription([
                'merchant_id' => $merchant->id,
                'plan_id' => $plan->id,
                'plan_code' => $plan->code,
                'plan_name' => $plan->name,
                'amount' => $plan->getPriceForCycle($request->billing_cycle),
                'billing_cycle' => $request->billing_cycle,
                'features' => $plan->features,
                'limits' => $plan->limits,
                'auto_renew' => true,
                'payment_method' => $request->payment_method
            ]);

            if ($useTrial) {
                $subscription->activateWithTrial($plan->trial_days);
            } else {
                $subscription->activate();
            }

            $subscription->save();

            // Invalidar cache do merchant
            cache()->forget("merchant_features_{$merchant->id}");
            cache()->forget("merchant_limits_{$merchant->id}");

            DB::commit();

            Log::info("Nova subscription criada para merchant {$merchant->id}", [
                'subscription_id' => $subscription->id,
                'plan' => $plan->code,
                'trial' => $useTrial
            ]);

            return response()->json([
                'message' => 'Subscription criada com sucesso',
                'subscription' => $subscription->toArray(),
                'trial_active' => $useTrial
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar subscription: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar subscription'], 500);
        }
    }

    /**
     * Fazer upgrade de plano
     */
    public function upgrade(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_code' => 'required|string|exists:subscription_plans,code',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'payment_method' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $merchant = Auth::user()->merchant;
            $currentSubscription = $merchant->activeSubscription;

            if (!$currentSubscription) {
                return response()->json(['error' => 'Nenhuma subscription ativa encontrada'], 404);
            }

            $newPlan = SubscriptionPlan::where('code', $request->plan_code)->first();

            // Verificar se é realmente um upgrade
            if (!$newPlan->isUpgradeFrom($currentSubscription->plan)) {
                return response()->json(['error' => 'Plano selecionado não é um upgrade válido'], 400);
            }

            // Calcular custos
            $costAnalysis = $this->planService->calculateUpgradeCost(
                $currentSubscription,
                $newPlan,
                $request->billing_cycle
            );

            DB::beginTransaction();

            // Cancelar subscription atual
            $currentSubscription->cancel('upgrade');

            // Criar nova subscription
            $newSubscription = $currentSubscription->upgradeTo($newPlan, $request->billing_cycle);
            $newSubscription->payment_method = $request->payment_method;
            $newSubscription->save();

            // Invalidar caches
            cache()->forget("merchant_features_{$merchant->id}");
            cache()->forget("merchant_limits_{$merchant->id}");

            DB::commit();

            Log::info("Upgrade realizado para merchant {$merchant->id}", [
                'from_plan' => $currentSubscription->plan_code,
                'to_plan' => $newPlan->code,
                'cost_analysis' => $costAnalysis
            ]);

            return response()->json([
                'message' => 'Upgrade realizado com sucesso',
                'subscription' => $newSubscription->toArray(),
                'cost_analysis' => $costAnalysis
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao fazer upgrade: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar upgrade'], 500);
        }
    }

    /**
     * Fazer downgrade de plano
     */
    public function downgrade(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'plan_code' => 'required|string|exists:subscription_plans,code',
            'reason' => 'string|max:500',
            'effective_date' => 'date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $merchant = Auth::user()->merchant;
            $currentSubscription = $merchant->activeSubscription;

            if (!$currentSubscription) {
                return response()->json(['error' => 'Nenhuma subscription ativa encontrada'], 404);
            }

            $newPlan = SubscriptionPlan::where('code', $request->plan_code)->first();

            // Verificar se é realmente um downgrade
            if ($newPlan->isUpgradeFrom($currentSubscription->plan)) {
                return response()->json(['error' => 'Plano selecionado não é um downgrade válido'], 400);
            }

            DB::beginTransaction();

            // Agendar downgrade para o final do período atual
            $effectiveDate = $request->effective_date ?
                \Carbon\Carbon::parse($request->effective_date) :
                $currentSubscription->expires_at;

            $currentSubscription->scheduleDowngrade($newPlan, $effectiveDate, $request->reason);

            DB::commit();

            Log::info("Downgrade agendado para merchant {$merchant->id}", [
                'from_plan' => $currentSubscription->plan_code,
                'to_plan' => $newPlan->code,
                'effective_date' => $effectiveDate->toISOString(),
                'reason' => $request->reason
            ]);

            return response()->json([
                'message' => 'Downgrade agendado com sucesso',
                'effective_date' => $effectiveDate->toISOString(),
                'new_plan' => $newPlan->toArray()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao agendar downgrade: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar downgrade'], 500);
        }
    }

    /**
     * Cancelar subscription
     */
    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'feedback' => 'string|max:1000',
            'immediate' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $merchant = Auth::user()->merchant;
            $subscription = $merchant->activeSubscription;

            if (!$subscription) {
                return response()->json(['error' => 'Nenhuma subscription ativa encontrada'], 404);
            }

            DB::beginTransaction();

            if ($request->boolean('immediate')) {
                $subscription->cancel($request->reason, now());
            } else {
                // Cancelar no final do período
                $subscription->cancel($request->reason, $subscription->expires_at);
            }

            // Salvar feedback se fornecido
            if ($request->feedback) {
                // Aqui você poderia criar uma tabela de feedback
                Log::info("Feedback de cancelamento recebido", [
                    'merchant_id' => $merchant->id,
                    'subscription_id' => $subscription->id,
                    'reason' => $request->reason,
                    'feedback' => $request->feedback
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Subscription cancelada com sucesso',
                'cancellation_date' => $subscription->cancelled_at?->toISOString(),
                'access_until' => $subscription->expires_at?->toISOString()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar subscription: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao processar cancelamento'], 500);
        }
    }

    /**
     * Reativar subscription cancelada
     */
    public function reactivate(): JsonResponse
    {
        try {
            $merchant = Auth::user()->merchant;
            $subscription = $merchant->latestSubscription;

            if (!$subscription || $subscription->status !== 'cancelled') {
                return response()->json(['error' => 'Nenhuma subscription cancelada encontrada'], 404);
            }

            // Verificar se ainda está dentro do período de reativação
            if ($subscription->expires_at && $subscription->expires_at->isPast()) {
                return response()->json(['error' => 'Período de reativação expirado'], 400);
            }

            DB::beginTransaction();

            $subscription->reactivate();

            // Invalidar caches
            cache()->forget("merchant_features_{$merchant->id}");
            cache()->forget("merchant_limits_{$merchant->id}");

            DB::commit();

            Log::info("Subscription reativada para merchant {$merchant->id}", [
                'subscription_id' => $subscription->id
            ]);

            return response()->json([
                'message' => 'Subscription reativada com sucesso',
                'subscription' => $subscription->toArray()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao reativar subscription: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao reativar subscription'], 500);
        }
    }

    /**
     * Alternar auto renovação
     */
    public function toggleAutoRenew(): JsonResponse
    {
        try {
            $merchant = Auth::user()->merchant;
            $subscription = $merchant->activeSubscription;

            if (!$subscription) {
                return response()->json(['error' => 'Nenhuma subscription ativa encontrada'], 404);
            }

            $subscription->auto_renew = !$subscription->auto_renew;
            $subscription->save();

            Log::info("Auto renovação alterada para merchant {$merchant->id}", [
                'subscription_id' => $subscription->id,
                'auto_renew' => $subscription->auto_renew
            ]);

            return response()->json([
                'message' => 'Auto renovação atualizada',
                'auto_renew' => $subscription->auto_renew
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao alterar auto renovação: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao atualizar auto renovação'], 500);
        }
    }

    /**
     * Obter histórico de subscriptions
     */
    public function history(): JsonResponse
    {
        try {
            $merchant = Auth::user()->merchant;

            $subscriptions = MerchantSubscription::where('merchant_id', $merchant->id)
                ->with('plan')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($subscription) {
                    return [
                        'id' => $subscription->id,
                        'plan_name' => $subscription->plan_name,
                        'plan_code' => $subscription->plan_code,
                        'status' => $subscription->status,
                        'amount' => $subscription->amount,
                        'billing_cycle' => $subscription->billing_cycle,
                        'started_at' => $subscription->started_at?->toISOString(),
                        'expires_at' => $subscription->expires_at?->toISOString(),
                        'cancelled_at' => $subscription->cancelled_at?->toISOString(),
                        'cancellation_reason' => $subscription->cancellation_reason,
                        'is_trial' => $subscription->is_trial,
                        'trial_ends_at' => $subscription->trial_ends_at?->toISOString()
                    ];
                });

            return response()->json([
                'subscriptions' => $subscriptions
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar histórico: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao buscar histórico'], 500);
        }
    }

    /**
     * Métodos auxiliares privados
     */
    private function getRecommendationReason(Merchant $merchant, SubscriptionPlan $recommendedPlan): string
    {
        $usage = $merchant->getUsageInfo();
        $currentPlan = $merchant->activeSubscription?->plan;

        if (!$currentPlan) {
            return "Recomendamos iniciar com o plano {$recommendedPlan->name}";
        }

        $highUsage = collect($usage)->filter(fn($data) => $data['percentage'] > 80);
        $lowUsage = collect($usage)->filter(fn($data) => $data['percentage'] < 30);

        if ($highUsage->isNotEmpty()) {
            $types = $highUsage->keys()->implode(', ');
            return "Seu uso de {$types} está alto. O plano {$recommendedPlan->name} oferece mais recursos.";
        }

        if ($lowUsage->count() === count($usage)) {
            return "Seu uso atual está baixo. O plano {$recommendedPlan->name} pode ser mais econômico.";
        }

        return "Baseado no seu perfil de uso, recomendamos o plano {$recommendedPlan->name}.";
    }

    private function canUpgrade(Merchant $merchant): bool
    {
        $currentPlan = $merchant->activeSubscription?->plan;
        if (!$currentPlan) {
            return true;
        }

        return SubscriptionPlan::where('price_monthly', '>', $currentPlan->price_monthly)
            ->exists();
    }

    private function canDowngrade(Merchant $merchant): bool
    {
        $currentPlan = $merchant->activeSubscription?->plan;
        if (!$currentPlan) {
            return false;
        }

        return SubscriptionPlan::where('price_monthly', '<', $currentPlan->price_monthly)
            ->exists();
    }
}
