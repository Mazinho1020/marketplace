<?php

namespace App\Affiliates\Services;

use App\Affiliates\Models\Affiliate;
use App\Affiliates\Models\AffiliateCommission;
use App\Affiliates\Models\AffiliateReferral;
use App\Affiliates\Models\AffiliatePayment;
use App\Merchants\Models\Merchant;
use App\Merchants\Models\MerchantSubscription;
use App\Core\Config\ConfigManager;
use App\Core\Cache\RedisCacheManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AffiliateService
{
    private $config;
    private $cache;

    public function __construct(ConfigManager $config, RedisCacheManager $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Processar referral de um novo merchant
     */
    public function processReferral(
        string $affiliateCode,
        Merchant $merchant,
        array $trackingData = []
    ): ?AffiliateReferral {

        $affiliate = Affiliate::where('affiliate_code', $affiliateCode)
            ->where('status', Affiliate::STATUS_APPROVED)
            ->first();

        if (!$affiliate) {
            Log::warning("Código de affiliate inválido: {$affiliateCode}");
            return null;
        }

        try {
            DB::beginTransaction();

            $referral = AffiliateReferral::create([
                'affiliate_id' => $affiliate->id,
                'merchant_id' => $merchant->id,
                'commission_rate' => $affiliate->commission_rate,
                'status' => AffiliateReferral::STATUS_ACTIVE,
                'referred_at' => now(),
                'utm_source' => $trackingData['utm_source'] ?? null,
                'utm_medium' => $trackingData['utm_medium'] ?? null,
                'utm_campaign' => $trackingData['utm_campaign'] ?? null,
                'referrer_url' => $trackingData['referrer_url'] ?? null,
                'ip_address' => $trackingData['ip_address'] ?? null,
                'user_agent' => $trackingData['user_agent'] ?? null
            ]);

            // Invalidar cache do affiliate
            $this->cache->forget("affiliate_stats_{$affiliate->id}");

            DB::commit();

            Log::info("Referral processado com sucesso", [
                'affiliate_id' => $affiliate->id,
                'merchant_id' => $merchant->id,
                'referral_id' => $referral->id
            ]);

            return $referral;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao processar referral: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Processar comissão quando merchant faz subscription
     */
    public function processCommission(
        MerchantSubscription $subscription,
        string $type = 'subscription'
    ): ?AffiliateCommission {

        $referral = AffiliateReferral::where('merchant_id', $subscription->merchant_id)
            ->where('status', AffiliateReferral::STATUS_ACTIVE)
            ->first();

        if (!$referral) {
            return null;
        }

        try {
            DB::beginTransaction();

            // Calcular comissão
            $commissionAmount = $this->calculateCommissionAmount(
                $subscription->amount,
                $referral->commission_rate,
                $type
            );

            $commission = AffiliateCommission::create([
                'affiliate_id' => $referral->affiliate_id,
                'referral_id' => $referral->id,
                'merchant_id' => $subscription->merchant_id,
                'subscription_id' => $subscription->id,
                'amount' => $commissionAmount,
                'commission_rate' => $referral->commission_rate,
                'status' => AffiliateCommission::STATUS_PENDING
            ]);

            // Converter referral se for primeira compra
            if (!$referral->isConverted()) {
                $referral->convert();
                $referral->recordFirstPayment();
            }

            // Invalidar caches
            $this->cache->forget("affiliate_stats_{$referral->affiliate_id}");
            $this->cache->forget("affiliate_commissions_{$referral->affiliate_id}");

            DB::commit();

            Log::info("Comissão processada", [
                'affiliate_id' => $referral->affiliate_id,
                'commission_id' => $commission->id,
                'amount' => $commissionAmount
            ]);

            return $commission;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao processar comissão: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcular valor da comissão
     */
    private function calculateCommissionAmount(
        float $subscriptionAmount,
        float $commissionRate,
        string $type
    ): float {

        $baseAmount = $subscriptionAmount;

        // Aplicar multiplicadores baseados no tipo
        $multiplier = match ($type) {
            'subscription' => 1.0,
            'renewal' => 0.5,      // 50% para renovações
            'upgrade' => 1.5,      // 150% para upgrades
            'trial_conversion' => 2.0, // 200% para conversões de trial
            default => 1.0
        };

        return $baseAmount * ($commissionRate / 100) * $multiplier;
    }

    /**
     * Processar pagamentos pendentes para affiliates
     */
    public function processPayments(int $minAmount = 50): array
    {
        $results = [
            'processed' => 0,
            'failed' => 0,
            'total_amount' => 0,
            'errors' => []
        ];

        try {
            // Buscar affiliates com comissões pendentes acima do valor mínimo
            $affiliatesWithPendingCommissions = DB::select("
                SELECT 
                    a.id,
                    a.name,
                    a.payment_method,
                    a.bank_details,
                    COUNT(ac.id) as commission_count,
                    SUM(ac.amount) as total_amount
                FROM affiliates a
                INNER JOIN affiliate_commissions ac ON a.id = ac.affiliate_id
                WHERE a.status = ? 
                AND ac.status = ?
                AND ac.created_at <= DATE_SUB(NOW(), INTERVAL 7 DAY)
                GROUP BY a.id
                HAVING total_amount >= ?
                ORDER BY total_amount DESC
            ", [Affiliate::STATUS_APPROVED, AffiliateCommission::STATUS_PENDING, $minAmount]);

            foreach ($affiliatesWithPendingCommissions as $affiliateData) {
                try {
                    $payment = $this->createPaymentForAffiliate(
                        $affiliateData->id,
                        $affiliateData->total_amount,
                        $affiliateData->commission_count,
                        json_decode($affiliateData->bank_details, true)
                    );

                    if ($payment) {
                        $results['processed']++;
                        $results['total_amount'] += $affiliateData->total_amount;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Falha ao criar pagamento para affiliate {$affiliateData->id}";
                    }
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Erro no affiliate {$affiliateData->id}: " . $e->getMessage();
                    Log::error("Erro ao processar pagamento do affiliate {$affiliateData->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error("Erro geral no processamento de pagamentos: " . $e->getMessage());
            $results['errors'][] = "Erro geral: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Criar pagamento para affiliate
     */
    private function createPaymentForAffiliate(
        int $affiliateId,
        float $amount,
        int $commissionCount,
        ?array $bankDetails
    ): ?AffiliatePayment {

        $affiliate = Affiliate::find($affiliateId);

        if (!$affiliate || !$affiliate->canReceivePayment()) {
            return null;
        }

        try {
            DB::beginTransaction();

            $payment = AffiliatePayment::create([
                'affiliate_id' => $affiliateId,
                'amount' => $amount,
                'commission_count' => $commissionCount,
                'payment_method' => $affiliate->payment_method,
                'status' => AffiliatePayment::STATUS_PENDING,
                'bank_details' => $bankDetails
            ]);

            // Associar comissões pendentes ao pagamento
            AffiliateCommission::where('affiliate_id', $affiliateId)
                ->where('status', AffiliateCommission::STATUS_PENDING)
                ->where('created_at', '<=', now()->subDays(7))
                ->update(['payment_id' => $payment->id]);

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Aprovar affiliate
     */
    public function approveAffiliate(int $affiliateId, float $commissionRate = null): bool
    {
        try {
            $affiliate = Affiliate::find($affiliateId);

            if (!$affiliate || $affiliate->status !== Affiliate::STATUS_PENDING) {
                return false;
            }

            $affiliate->approve($commissionRate);

            // Invalidar cache
            $this->cache->forget("affiliate_stats_{$affiliateId}");

            Log::info("Affiliate aprovado", [
                'affiliate_id' => $affiliateId,
                'commission_rate' => $commissionRate ?? $affiliate->commission_rate
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao aprovar affiliate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter estatísticas do programa de afiliados
     */
    public function getProgramStatistics(): array
    {
        return $this->cache->remember('affiliate_program_stats', function () {
            $stats = DB::selectOne("
                SELECT 
                    COUNT(DISTINCT a.id) as total_affiliates,
                    COUNT(DISTINCT CASE WHEN a.status = 'approved' THEN a.id END) as active_affiliates,
                    COUNT(DISTINCT ar.id) as total_referrals,
                    COUNT(DISTINCT CASE WHEN ar.status = 'converted' THEN ar.id END) as converted_referrals,
                    COALESCE(SUM(ac.amount), 0) as total_commissions,
                    COALESCE(SUM(CASE WHEN ac.status = 'paid' THEN ac.amount END), 0) as paid_commissions,
                    COALESCE(SUM(CASE WHEN ac.status = 'pending' THEN ac.amount END), 0) as pending_commissions,
                    ROUND(
                        COALESCE(
                            COUNT(CASE WHEN ar.status = 'converted' THEN 1 END) * 100.0 / 
                            NULLIF(COUNT(ar.id), 0), 0
                        ), 2
                    ) as conversion_rate,
                    ROUND(
                        COALESCE(
                            SUM(CASE WHEN ac.status = 'paid' THEN ac.amount END) * 100.0 /
                            NULLIF(SUM(ac.amount), 0), 0
                        ), 2
                    ) as payment_rate
                FROM affiliates a
                LEFT JOIN affiliate_referrals ar ON a.id = ar.affiliate_id
                LEFT JOIN affiliate_commissions ac ON a.id = ac.affiliate_id
            ");

            return [
                'total_affiliates' => (int) ($stats->total_affiliates ?? 0),
                'active_affiliates' => (int) ($stats->active_affiliates ?? 0),
                'total_referrals' => (int) ($stats->total_referrals ?? 0),
                'converted_referrals' => (int) ($stats->converted_referrals ?? 0),
                'conversion_rate' => (float) ($stats->conversion_rate ?? 0),
                'total_commissions' => (float) ($stats->total_commissions ?? 0),
                'paid_commissions' => (float) ($stats->paid_commissions ?? 0),
                'pending_commissions' => (float) ($stats->pending_commissions ?? 0),
                'payment_rate' => (float) ($stats->payment_rate ?? 0),
                'avg_commission_per_affiliate' => $stats->active_affiliates > 0 ?
                    round($stats->total_commissions / $stats->active_affiliates, 2) : 0
            ];
        }, 3600); // 1 hora
    }

    /**
     * Obter top performers
     */
    public function getTopPerformers(int $limit = 10, string $period = '30d'): Collection
    {
        $days = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };

        return collect(DB::select("
            SELECT 
                a.id,
                a.name,
                a.affiliate_code,
                COUNT(DISTINCT ar.id) as referrals,
                COUNT(DISTINCT CASE WHEN ar.status = 'converted' THEN ar.id END) as conversions,
                COALESCE(SUM(ac.amount), 0) as total_commissions,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN ar.status = 'converted' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(ar.id), 0), 0
                    ), 2
                ) as conversion_rate
            FROM affiliates a
            LEFT JOIN affiliate_referrals ar ON a.id = ar.affiliate_id 
                AND ar.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            LEFT JOIN affiliate_commissions ac ON a.id = ac.affiliate_id 
                AND ac.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            WHERE a.status = 'approved'
            GROUP BY a.id
            HAVING total_commissions > 0
            ORDER BY total_commissions DESC
            LIMIT ?
        ", [$days, $days, $limit]));
    }

    /**
     * Validar código de referral
     */
    public function validateReferralCode(string $code): ?Affiliate
    {
        return Affiliate::where('affiliate_code', $code)
            ->where('status', Affiliate::STATUS_APPROVED)
            ->first();
    }

    /**
     * Gerar relatório de performance
     */
    public function generatePerformanceReport(int $affiliateId, string $period = '30d'): array
    {
        $affiliate = Affiliate::find($affiliateId);

        if (!$affiliate) {
            throw new \Exception("Affiliate não encontrado");
        }

        $days = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 30
        };

        $stats = $affiliate->getStatistics();
        $monthlyPerformance = $affiliate->getMonthlyPerformance($days / 30);

        return [
            'affiliate' => $affiliate->toArray(),
            'period' => $period,
            'statistics' => $stats,
            'monthly_performance' => $monthlyPerformance,
            'top_referral_sources' => $this->getTopReferralSources($affiliateId, $days),
            'commission_breakdown' => $this->getCommissionBreakdown($affiliateId, $days)
        ];
    }

    /**
     * Obter principais fontes de referral
     */
    private function getTopReferralSources(int $affiliateId, int $days): array
    {
        return DB::select("
            SELECT 
                COALESCE(utm_source, 'Direct') as source,
                COUNT(*) as count,
                COUNT(CASE WHEN status = 'converted' THEN 1 END) as conversions
            FROM affiliate_referrals
            WHERE affiliate_id = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY utm_source
            ORDER BY count DESC
            LIMIT 5
        ", [$affiliateId, $days]);
    }

    /**
     * Obter breakdown de comissões
     */
    private function getCommissionBreakdown(int $affiliateId, int $days): array
    {
        return DB::select("
            SELECT 
                status,
                COUNT(*) as count,
                SUM(amount) as total
            FROM affiliate_commissions
            WHERE affiliate_id = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY status
            ORDER BY total DESC
        ", [$affiliateId, $days]);
    }
}
