<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Core\Cache\RedisCacheManager;
use App\Core\Config\ConfigManager;
use App\Merchants\Models\Merchant;

class CheckPlanFeature
{
    private $cache;
    private $config;

    public function __construct(RedisCacheManager $cache, ConfigManager $config)
    {
        $this->cache = $cache;
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature)
    {
        // Verificar se usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Assumindo que o usuário tem um merchant associado
        $merchant = $this->getMerchant($user);

        if (!$merchant) {
            return $this->handleNoMerchant($request);
        }

        // Verificar se tem a feature
        if (!$this->hasFeature($merchant, $feature)) {
            return $this->handleFeatureBlocked($request, $feature, $merchant);
        }

        // Verificar se está dentro dos limites
        if (!$this->isWithinLimits($merchant)) {
            return $this->handleLimitExceeded($request, $merchant);
        }

        return $next($request);
    }

    /**
     * Verificar se o merchant tem a feature
     */
    private function hasFeature(Merchant $merchant, string $feature): bool
    {
        // Buscar do cache primeiro
        $features = $this->cache->getMerchantFeatures($merchant->id);

        if ($features === null) {
            // Cache miss, buscar do banco
            $features = $merchant->getActiveFeatures();

            // Armazenar no cache
            $this->cache->put("merchant_features_{$merchant->id}", $features, 3600);
        }

        return in_array($feature, $features);
    }

    /**
     * Verificar se está dentro dos limites
     */
    private function isWithinLimits(Merchant $merchant): bool
    {
        // Buscar do cache primeiro
        $cacheKey = "merchant_limits_check_{$merchant->id}";

        return $this->cache->remember($cacheKey, function () use ($merchant) {
            return $merchant->isWithinLimits();
        }, 300); // 5 minutos para limites
    }

    /**
     * Obter merchant do usuário
     */
    private function getMerchant($user): ?Merchant
    {
        // Adaptar conforme sua estrutura de relacionamento
        if (method_exists($user, 'merchant')) {
            return $user->merchant;
        }

        // Alternativa: buscar por email ou outro campo
        return Merchant::where('email', $user->email)->first();
    }

    /**
     * Tratar caso sem merchant
     */
    private function handleNoMerchant(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'no_merchant',
                'message' => 'Empresa não encontrada',
                'redirect_url' => route('merchant.setup')
            ], 403);
        }

        return redirect()->route('merchant.setup')
            ->with('error', 'Complete o cadastro da sua empresa para continuar');
    }

    /**
     * Tratar feature bloqueada
     */
    private function handleFeatureBlocked(Request $request, string $feature, Merchant $merchant)
    {
        $subscription = $merchant->activeSubscription;
        $currentPlan = $subscription ? $subscription->plan_name : 'Nenhum';
        $requiredPlan = $this->getRequiredPlanFor($feature);

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'feature_blocked',
                'feature' => $feature,
                'current_plan' => $currentPlan,
                'required_plan' => $requiredPlan,
                'upgrade_url' => route('merchant.plans.upgrade'),
                'message' => "O recurso '{$this->getFeatureName($feature)}' não está disponível no seu plano atual."
            ], 403);
        }

        // Para requisições web, redirecionar para upgrade
        return redirect()->route('merchant.plans.upgrade')
            ->with('blocked_feature', $feature)
            ->with('current_plan', $currentPlan)
            ->with('required_plan', $requiredPlan)
            ->with('error', "Para acessar '{$this->getFeatureName($feature)}', você precisa fazer upgrade para o plano {$requiredPlan}.");
    }

    /**
     * Tratar limite excedido
     */
    private function handleLimitExceeded(Request $request, Merchant $merchant)
    {
        $usageInfo = $merchant->getUsageInfo();

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'limits_exceeded',
                'usage' => $usageInfo,
                'upgrade_url' => route('merchant.plans.upgrade'),
                'message' => 'Você atingiu o limite do seu plano atual.'
            ], 403);
        }

        return redirect()->route('merchant.plans.upgrade')
            ->with('limits_exceeded', true)
            ->with('usage_info', $usageInfo)
            ->with('error', 'Você atingiu o limite do seu plano. Faça upgrade para continuar.');
    }

    /**
     * Obter plano mínimo necessário para uma feature
     */
    private function getRequiredPlanFor(string $feature): string
    {
        $featurePlans = [
            'pdv' => 'Premium',
            'delivery' => 'Premium',
            'relatorios_avancados' => 'Premium',
            'multi_empresa' => 'Enterprise',
            'api' => 'Enterprise',
            'suporte_24h' => 'Enterprise'
        ];

        return $featurePlans[$feature] ?? 'Premium';
    }

    /**
     * Obter nome amigável da feature
     */
    private function getFeatureName(string $feature): string
    {
        $featureNames = [
            'pdv' => 'PDV',
            'delivery' => 'Sistema de Delivery',
            'relatorios_avancados' => 'Relatórios Avançados',
            'multi_empresa' => 'Multi-empresa',
            'api' => 'API',
            'suporte_24h' => 'Suporte 24h'
        ];

        return $featureNames[$feature] ?? ucfirst(str_replace('_', ' ', $feature));
    }
}

/**
 * Middleware específico para verificar limites de usuários
 */
class CheckUserLimit
{
    private $cache;

    public function __construct(RedisCacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $merchant = $user->merchant ?? Merchant::where('email', $user->email)->first();

        if (!$merchant) {
            return redirect()->route('merchant.setup');
        }

        // Verificar limite de usuários ao tentar criar novo
        if ($request->isMethod('post') && $request->routeIs('users.store')) {
            $currentUserCount = $merchant->users()->count();
            $userLimit = $merchant->getActiveLimits()['users'] ?? 1;

            if ($currentUserCount >= $userLimit) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'user_limit_exceeded',
                        'current' => $currentUserCount,
                        'limit' => $userLimit,
                        'upgrade_url' => route('merchant.plans.upgrade')
                    ], 403);
                }

                return redirect()->back()
                    ->with('error', "Limite de {$userLimit} usuários atingido. Faça upgrade do seu plano.");
            }
        }

        return $next($request);
    }
}

/**
 * Middleware para verificar status da assinatura
 */
class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $merchant = $user->merchant ?? Merchant::where('email', $user->email)->first();

        if (!$merchant) {
            return redirect()->route('merchant.setup');
        }

        $subscription = $merchant->activeSubscription;

        // Sem assinatura ativa
        if (!$subscription) {
            return $this->redirectToPlans($request, 'Selecione um plano para continuar.');
        }

        // Assinatura suspensa
        if ($subscription->status === 'suspended') {
            return $this->redirectToPlans($request, 'Sua assinatura está suspensa. Regularize seu pagamento.');
        }

        // Assinatura expirada
        if ($subscription->isExpired()) {
            return $this->redirectToPlans($request, 'Sua assinatura expirou. Renove para continuar.');
        }

        // Trial expirado
        if ($subscription->status === 'trial' && $subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            return $this->redirectToPlans($request, 'Seu período trial expirou. Escolha um plano para continuar.');
        }

        return $next($request);
    }

    private function redirectToPlans(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'subscription_required',
                'message' => $message,
                'plans_url' => route('merchant.plans.index')
            ], 402); // Payment Required
        }

        return redirect()->route('merchant.plans.index')
            ->with('warning', $message);
    }
}
