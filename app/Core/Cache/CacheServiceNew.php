<?php

namespace App\Core\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    const MERCHANT_TTL = 3600; // 1 hora
    const PLAN_TTL = 7200; // 2 horas  
    const STATS_TTL = 900; // 15 minutos
    const CONFIG_TTL = 86400; // 24 horas

    /**
     * Obter recursos do merchant com cache
     */
    public function getMerchantFeatures($merchantId): array
    {
        $cacheKey = "merchant_features:{$merchantId}";

        return Cache::remember($cacheKey, self::PLAN_TTL, function () use ($merchantId) {
            // Buscar diretamente das views do sistema
            $merchant = DB::table('merchants')->where('id', $merchantId)->first();

            if (!$merchant || !$merchant->subscription_plan_id) {
                return $this->getDefaultFeatures();
            }

            // Buscar plano do merchant
            $plan = DB::table('subscription_plans')
                ->where('id', $merchant->subscription_plan_id)
                ->first();

            if (!$plan || !$plan->recursos) {
                return $this->getDefaultFeatures();
            }

            return json_decode($plan->recursos, true) ?? $this->getDefaultFeatures();
        });
    }

    /**
     * Obter limites do merchant com cache
     */
    public function getMerchantLimits($merchantId): array
    {
        $cacheKey = "merchant_limits:{$merchantId}";

        return Cache::remember($cacheKey, self::PLAN_TTL, function () use ($merchantId) {
            // Buscar da view merchants
            $merchant = DB::table('merchants')->where('id', $merchantId)->first();

            if (!$merchant || !$merchant->subscription_plan_id) {
                return $this->getDefaultLimits();
            }

            // Buscar plano do merchant  
            $plan = DB::table('subscription_plans')
                ->where('id', $merchant->subscription_plan_id)
                ->first();

            if (!$plan || !$plan->limites) {
                return $this->getDefaultLimits();
            }

            return json_decode($plan->limites, true) ?? $this->getDefaultLimits();
        });
    }

    /**
     * Obter estatísticas dos gateways
     */
    public function getGatewayStats($merchantId = null): array
    {
        $cacheKey = $merchantId ? "gateway_stats:{$merchantId}" : "gateway_stats:global";

        return Cache::remember($cacheKey, self::STATS_TTL, function () use ($merchantId) {
            $query = "
                SELECT 
                    g.nome,
                    g.provedor,
                    g.ativo,
                    COUNT(t.id) as total_transacoes,
                    COALESCE(SUM(CASE WHEN t.status = 'aprovado' THEN t.valor ELSE 0 END), 0) as volume_aprovado,
                    COALESCE(AVG(CASE WHEN t.status = 'aprovado' THEN t.valor END), 0) as ticket_medio
                FROM afi_plan_gateways g
                LEFT JOIN payment_transactions t ON g.id = t.gateway_id
            ";

            if ($merchantId) {
                $query .= " WHERE t.merchant_id = ? ";
            }

            $query .= " GROUP BY g.id, g.nome, g.provedor, g.ativo";

            return DB::select($query, $merchantId ? [$merchantId] : []);
        });
    }

    /**
     * Obter métodos de pagamento disponíveis
     */
    public function getPaymentMethods($merchantId = null): array
    {
        $cacheKey = $merchantId ? "payment_methods:{$merchantId}" : "payment_methods:global";

        return Cache::remember($cacheKey, self::CONFIG_TTL, function () use ($merchantId) {
            $query = "
                SELECT 
                    g.codigo,
                    g.nome,
                    g.provedor,
                    g.ativo,
                    g.configuracoes
                FROM afi_plan_gateways g
                WHERE g.ativo = 1
            ";

            if ($merchantId) {
                $query .= " AND (g.empresa_id = ? OR g.empresa_id = 0)";
                return DB::select($query, [$merchantId]);
            }

            return DB::select($query);
        });
    }

    /**
     * Aquecer cache com dados mais utilizados
     */
    public function warmupCache($merchantId = null): void
    {
        // Se merchant específico, aquecer apenas seus dados
        if ($merchantId) {
            $this->getMerchantFeatures($merchantId);
            $this->getMerchantLimits($merchantId);
            $this->getGatewayStats($merchantId);
            $this->getPaymentMethods($merchantId);
            return;
        }

        // Aquecer cache global
        $this->getGatewayStats();
        $this->getPaymentMethods();

        // Aquecer cache dos merchants mais ativos
        $activeMerchants = DB::select("
            SELECT m.id 
            FROM merchants m 
            WHERE m.subscription_status = 'active' 
            LIMIT 10
        ");

        foreach ($activeMerchants as $merchant) {
            $this->getMerchantFeatures($merchant->id);
            $this->getMerchantLimits($merchant->id);
        }
    }

    /**
     * Limpar cache específico do merchant
     */
    public function clearMerchantCache($merchantId): void
    {
        Cache::forget("merchant_features:{$merchantId}");
        Cache::forget("merchant_limits:{$merchantId}");
        Cache::forget("gateway_stats:{$merchantId}");
        Cache::forget("payment_methods:{$merchantId}");
    }

    /**
     * Limpar todo o cache do sistema
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Features padrão para merchants sem plano
     */
    private function getDefaultFeatures(): array
    {
        return [
            'pdv_enabled' => true,
            'relatorios_basicos' => true,
            'suporte_email' => true,
            'customizacao_basica' => false,
            'api_access' => false,
            'webhook_enabled' => false,
            'multi_gateway' => false,
            'relatorios_avancados' => false,
            'programa_afiliados' => false,
            'customizacao_avancada' => false
        ];
    }

    /**
     * Limites padrão para merchants sem plano
     */
    private function getDefaultLimits(): array
    {
        return [
            'transacoes_mes' => 100,
            'usuarios' => 1,
            'produtos' => 50,
            'clientes' => 100,
            'storage_mb' => 100,
            'api_calls_dia' => 0,
            'webhooks_dia' => 0
        ];
    }
}
