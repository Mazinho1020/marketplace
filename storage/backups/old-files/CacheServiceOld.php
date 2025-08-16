<?php

namespace App\Core\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    const DEFAULT_TTL = 3600; // 1 hora
    const CONFIG_TTL = 7200;  // 2 horas
    const PLAN_TTL = 1800;    // 30 minutos

    /**
     * Cache das configurações do merchant
     */
    public function getMerchantConfig($merchantId, $key, $default = null)
    {
        $cacheKey = "merchant_config:{$merchantId}:{$key}";
        
        return Cache::remember($cacheKey, self::CONFIG_TTL, function() use ($merchantId, $key, $default) {
            $config = \App\Models\AfiPlan\Configuration::where('empresa_id', $merchantId)
                ->where('chave', $key)
                ->first();
                
            return $config ? $config->valor : $default;
        });
    }

    /**
     * Cache das features do plano do merchant
     */
    public function getMerchantFeatures($merchantId)
    {
        $cacheKey = "merchant_features:{$merchantId}";
        
        return Cache::remember($cacheKey, self::PLAN_TTL, function() use ($merchantId) {
            // Buscar diretamente das views do sistema
            $merchant = \DB::table('merchants')->where('id', $merchantId)->first();
            
            if (!$merchant || !$merchant->subscription_plan_id) {
                return $this->getDefaultFeatures();
            }

            // Buscar plano do merchant
            $plan = \DB::table('subscription_plans')
                ->where('id', $merchant->subscription_plan_id)
                ->first();

            if (!$plan || !$plan->recursos) {
                return $this->getDefaultFeatures();
            }

            return json_decode($plan->recursos, true) ?? $this->getDefaultFeatures();
        });
    }

    /**
     * Cache dos limites do plano do merchant
     */
    public function getMerchantLimits($merchantId)
    {
        $cacheKey = "merchant_limits:{$merchantId}";
        
        return Cache::remember($cacheKey, self::PLAN_TTL, function() use ($merchantId) {
            $subscription = \App\Models\AfiPlan\Subscription::where('funforcli_id', $merchantId)
                ->where('status', 'ativo')
                ->with('plan')
                ->first();
                
            if (!$subscription || !$subscription->plan) {
                return [];
            }
            
            $limites = $subscription->plan->limites;
            return is_string($limites) ? json_decode($limites, true) : $limites;
        });
    }

    /**
     * Cache das estatísticas do gateway
     */
    public function getGatewayStats($gatewayId, $period = '30d')
    {
        $cacheKey = "gateway_stats:{$gatewayId}:{$period}";
        
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function() use ($gatewayId, $period) {
            $days = match($period) {
                '7d' => 7,
                '30d' => 30,
                '90d' => 90,
                default => 30
            };
            
            return \DB::selectOne("
                SELECT 
                    COUNT(*) as total_transactions,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as successful,
                    COALESCE(SUM(final_amount), 0) as total_volume,
                    ROUND(
                        COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 2
                    ) as success_rate
                FROM payment_transactions 
                WHERE gateway_id = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ", [$gatewayId, $days]);
        });
    }

    /**
     * Cache dos métodos de pagamento disponíveis
     */
    public function getAvailablePaymentMethods($merchantId)
    {
        $cacheKey = "payment_methods:{$merchantId}";
        
        return Cache::remember($cacheKey, self::PLAN_TTL, function() use ($merchantId) {
            $features = $this->getMerchantFeatures($merchantId);
            
            $allMethods = [
                'pix' => 'PIX',
                'credit_card' => 'Cartão de Crédito',
                'debit_card' => 'Cartão de Débito',
                'bank_slip' => 'Boleto',
                'bank_transfer' => 'Transferência Bancária'
            ];
            
            if (empty($features) || !isset($features['payment_methods'])) {
                return $allMethods; // Se não há restrições, libera todos
            }
            
            $allowedMethods = $features['payment_methods'];
            return array_intersect_key($allMethods, array_flip($allowedMethods));
        });
    }

    /**
     * Limpar cache específico do merchant
     */
    public function clearMerchantCache($merchantId)
    {
        $patterns = [
            "merchant_config:{$merchantId}:*",
            "merchant_features:{$merchantId}",
            "merchant_limits:{$merchantId}",
            "payment_methods:{$merchantId}"
        ];
        
        foreach ($patterns as $pattern) {
            try {
                Cache::forget($pattern);
            } catch (\Exception $e) {
                Log::warning("Erro ao limpar cache: {$pattern}", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Limpar cache de gateway
     */
    public function clearGatewayCache($gatewayId)
    {
        $periods = ['7d', '30d', '90d'];
        
        foreach ($periods as $period) {
            try {
                Cache::forget("gateway_stats:{$gatewayId}:{$period}");
            } catch (\Exception $e) {
                Log::warning("Erro ao limpar cache do gateway: {$gatewayId}", ['error' => $e->getMessage()]);
            }
        }
    }

    /**
     * Aquecimento de cache - popular caches importantes
     */
    public function warmupCache($merchantId)
    {
        try {
            $this->getMerchantFeatures($merchantId);
            $this->getMerchantLimits($merchantId);
            $this->getAvailablePaymentMethods($merchantId);
            
            Log::info("Cache aquecido para merchant: {$merchantId}");
        } catch (\Exception $e) {
            Log::error("Erro ao aquecer cache para merchant: {$merchantId}", ['error' => $e->getMessage()]);
        }
    }
}
