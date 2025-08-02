<?php

namespace App\Core\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * Gerenciador de Cache Redis
 * Implementa estratégias de cache específicas para o sistema
 */
class RedisCacheManager
{
    // TTL por tipo de cache
    private const CACHE_TTLS = [
        'config' => 3600,        // 1 hora - Configurações
        'features' => 3600,      // 1 hora - Features de planos
        'limits' => 3600,        // 1 hora - Limites de planos
        'transactions' => 300,   // 5 min - Transações ativas
        'reports' => 86400,      // 24h - Relatórios
        'affiliate_rates' => 3600, // 1 hora - Taxas de comissão
    ];

    /**
     * Armazenar dados com TTL específico
     */
    public function put(string $key, $value, ?int $ttl = null): bool
    {
        try {
            $cacheType = $this->getCacheType($key);
            $finalTtl = $ttl ?? self::CACHE_TTLS[$cacheType] ?? 3600;

            return Cache::put($key, $value, $finalTtl);
        } catch (\Exception $e) {
            Log::error("Erro ao armazenar no cache", [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Buscar dados do cache
     */
    public function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::warning("Erro ao buscar do cache", [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Buscar ou executar callback
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        try {
            $cacheType = $this->getCacheType($key);
            $finalTtl = $ttl ?? self::CACHE_TTLS[$cacheType] ?? 3600;

            return Cache::remember($key, $finalTtl, $callback);
        } catch (\Exception $e) {
            Log::warning("Erro no cache remember", [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $callback();
        }
    }

    /**
     * Remover chave específica
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::error("Erro ao remover do cache", [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Cache para features do comerciante
     */
    public function getMerchantFeatures(int $merchantId): ?array
    {
        $key = "merchant_features_{$merchantId}";

        return $this->remember($key, function () use ($merchantId) {
            $merchant = \App\Models\Merchant::find($merchantId);
            return $merchant ? $merchant->getActiveFeatures() : null;
        });
    }

    /**
     * Cache para limites do comerciante
     */
    public function getMerchantLimits(int $merchantId): ?array
    {
        $key = "merchant_limits_{$merchantId}";

        return $this->remember($key, function () use ($merchantId) {
            $merchant = \App\Models\Merchant::find($merchantId);
            return $merchant ? $merchant->getActiveLimits() : null;
        });
    }

    /**
     * Cache para taxas de comissão do afiliado
     */
    public function getAffiliateRates(int $affiliateId): ?array
    {
        $key = "affiliate_rates_{$affiliateId}";

        return $this->remember($key, function () use ($affiliateId) {
            $affiliate = \App\Models\Affiliate::find($affiliateId);
            return $affiliate ? [
                'rate' => $affiliate->commission_rate,
                'tier' => $affiliate->tier,
                'effective_rate' => $affiliate->getEffectiveCommissionRate()
            ] : null;
        });
    }

    /**
     * Cache para dashboard
     */
    public function getDashboardStats(): array
    {
        $key = "dashboard_stats";

        return $this->remember($key, function () {
            return [
                'mrr_total' => $this->calculateMRR(),
                'active_subscribers' => $this->getActiveSubscribers(),
                'revenue_today' => $this->getTodayRevenue(),
                'active_affiliates' => $this->getActiveAffiliates(),
                'churn_rate' => $this->calculateChurnRate(),
            ];
        }, 300); // 5 minutos para dashboard
    }

    /**
     * Invalidar cache relacionado ao comerciante
     */
    public function invalidateMerchantCache(int $merchantId): void
    {
        $keys = [
            "merchant_features_{$merchantId}",
            "merchant_limits_{$merchantId}",
            "merchant_subscription_{$merchantId}",
        ];

        foreach ($keys as $key) {
            $this->forget($key);
        }

        // Invalidar dashboard se necessário
        $this->forget('dashboard_stats');
    }

    /**
     * Invalidar cache relacionado ao afiliado
     */
    public function invalidateAffiliateCache(int $affiliateId): void
    {
        $keys = [
            "affiliate_rates_{$affiliateId}",
            "affiliate_stats_{$affiliateId}",
        ];

        foreach ($keys as $key) {
            $this->forget($key);
        }
    }

    /**
     * Invalidar cache de configurações
     */
    public function invalidateConfigCache(int $empresaId = 1): void
    {
        // Em Redis, usar SCAN para encontrar todas as chaves config:empresa_id:*
        if (Redis::connection()) {
            $pattern = "config:{$empresaId}:*";
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            // Fallback: flush todo o cache (não ideal)
            Cache::flush();
        }
    }

    /**
     * Limpar cache antigo
     */
    public function cleanupExpiredCache(): int
    {
        $cleaned = 0;

        try {
            // Redis lida com TTL automaticamente
            // Aqui podemos implementar limpeza manual se necessário

            Log::info("Cache cleanup executado", ['keys_cleaned' => $cleaned]);
        } catch (\Exception $e) {
            Log::error("Erro no cleanup de cache", ['error' => $e->getMessage()]);
        }

        return $cleaned;
    }

    /**
     * Determinar tipo de cache pela chave
     */
    private function getCacheType(string $key): string
    {
        if (str_contains($key, 'config:')) return 'config';
        if (str_contains($key, 'merchant_features')) return 'features';
        if (str_contains($key, 'merchant_limits')) return 'limits';
        if (str_contains($key, 'transaction')) return 'transactions';
        if (str_contains($key, 'report') || str_contains($key, 'dashboard')) return 'reports';
        if (str_contains($key, 'affiliate_rates')) return 'affiliate_rates';

        return 'default';
    }

    /**
     * Métodos auxiliares para dashboard
     */
    private function calculateMRR(): float
    {
        return \DB::selectOne("
            SELECT 
                (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'active' AND billing_cycle = 'monthly') +
                (SELECT COALESCE(SUM(amount)/12, 0) FROM merchant_subscriptions WHERE status = 'active' AND billing_cycle = 'yearly') as mrr
        ")->mrr ?? 0;
    }

    private function getActiveSubscribers(): int
    {
        return \DB::table('merchant_subscriptions')
            ->where('status', 'active')
            ->count();
    }

    private function getTodayRevenue(): float
    {
        return \DB::table('payment_transactions')
            ->whereDate('created_at', today())
            ->where('status', 'approved')
            ->sum('amount_final');
    }

    private function getActiveAffiliates(): int
    {
        return \DB::table('affiliates')
            ->where('status', 'active')
            ->count();
    }

    private function calculateChurnRate(): float
    {
        $result = \DB::selectOne("
            SELECT 
                ROUND(
                    (COUNT(CASE WHEN status = 'cancelled' AND cancelled_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) * 100.0) / 
                    COUNT(CASE WHEN status IN ('active', 'cancelled') AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END)
                , 2) as churn_rate
            FROM merchant_subscriptions
        ");

        return $result->churn_rate ?? 0;
    }
}
