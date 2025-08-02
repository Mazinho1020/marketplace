<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Página principal de relatórios
     */
    public function index()
    {
        // Resumo executivo
        $executiveSummary = $this->getExecutiveSummary();

        // KPIs principais
        $kpis = $this->getMainKPIs();

        // Relatórios disponíveis
        $availableReports = $this->getAvailableReports();

        return view('admin.reports.index', compact(
            'executiveSummary',
            'kpis',
            'availableReports'
        ));
    }

    /**
     * Relatório de receita
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', '12m');
        $groupBy = $request->get('group_by', 'month');

        // Receita por período
        $revenueData = $this->getRevenueData($period, $groupBy);

        // Receita por plano
        $revenueByPlan = $this->getRevenueByPlan($period);

        // Receita por gateway
        $revenueByGateway = $this->getRevenueByGateway($period);

        // Projeções
        $projections = $this->getRevenueProjections();

        // MRR (Monthly Recurring Revenue)
        $mrrData = $this->getMRRData($period);

        return view('admin.reports.revenue', compact(
            'revenueData',
            'revenueByPlan',
            'revenueByGateway',
            'projections',
            'mrrData',
            'period',
            'groupBy'
        ));
    }

    /**
     * Relatório de merchants
     */
    public function merchants(Request $request)
    {
        $period = $request->get('period', '12m');

        // Crescimento de merchants
        $merchantGrowth = $this->getMerchantGrowth($period);

        // Segmentação por plano
        $planSegmentation = $this->getMerchantPlanSegmentation();

        // Churn analysis
        $churnAnalysis = $this->getMerchantChurnAnalysis($period);

        // LTV (Lifetime Value)
        $ltvData = $this->getMerchantLTVData();

        // Top merchants por receita
        $topMerchants = $this->getTopMerchants($period);

        // Análise de uso por plano
        $usageAnalysis = $this->getUsageAnalysis();

        return view('admin.reports.merchants', compact(
            'merchantGrowth',
            'planSegmentation',
            'churnAnalysis',
            'ltvData',
            'topMerchants',
            'usageAnalysis',
            'period'
        ));
    }

    /**
     * Relatório de afiliados
     */
    public function affiliates(Request $request)
    {
        $period = $request->get('period', '12m');

        // Performance geral do programa
        $programPerformance = $this->getAffiliateProgramPerformance($period);

        // Top performers
        $topPerformers = $this->getTopAffiliatePerformers($period);

        // Análise de comissões
        $commissionAnalysis = $this->getCommissionAnalysis($period);

        // Conversion rates
        $conversionData = $this->getAffiliateConversionData($period);

        // ROI do programa
        $programROI = $this->getAffiliateProgramROI($period);

        // Fontes de tráfego
        $trafficSources = $this->getAffiliateTrafficSources($period);

        return view('admin.reports.affiliates', compact(
            'programPerformance',
            'topPerformers',
            'commissionAnalysis',
            'conversionData',
            'programROI',
            'trafficSources',
            'period'
        ));
    }

    /**
     * Relatório de assinaturas
     */
    public function subscriptions(Request $request)
    {
        $period = $request->get('period', '12m');

        // Métricas de assinaturas
        $subscriptionMetrics = $this->getSubscriptionMetrics($period);

        // Cohort analysis
        $cohortAnalysis = $this->getCohortAnalysis();

        // Trial to paid conversion
        $trialConversion = $this->getTrialConversionData($period);

        // Plan upgrade/downgrade analysis
        $planChanges = $this->getPlanChangeAnalysis($period);

        // Seasonal patterns
        $seasonalPatterns = $this->getSeasonalPatterns();

        // Retention analysis
        $retentionAnalysis = $this->getRetentionAnalysis($period);

        return view('admin.reports.subscriptions', compact(
            'subscriptionMetrics',
            'cohortAnalysis',
            'trialConversion',
            'planChanges',
            'seasonalPatterns',
            'retentionAnalysis',
            'period'
        ));
    }

    /**
     * Exportar relatório
     */
    public function export($type, Request $request)
    {
        $format = $request->get('format', 'csv');
        $period = $request->get('period', '30d');

        $data = match ($type) {
            'revenue' => $this->getRevenueExportData($period),
            'merchants' => $this->getMerchantsExportData($period),
            'affiliates' => $this->getAffiliatesExportData($period),
            'subscriptions' => $this->getSubscriptionsExportData($period),
            'transactions' => $this->getTransactionsExportData($period),
            default => []
        };

        return $this->exportData($data, $type, $format);
    }

    /**
     * Métodos auxiliares privados
     */

    private function getExecutiveSummary(): array
    {
        $summary = DB::selectOne("
            SELECT 
                (SELECT COUNT(*) FROM merchants) as total_merchants,
                (SELECT COUNT(*) FROM merchant_subscriptions WHERE status = 'active') as active_subscriptions,
                (SELECT COUNT(*) FROM affiliates WHERE status = 'approved') as active_affiliates,
                (SELECT COALESCE(SUM(amount), 0) FROM merchant_subscriptions WHERE status = 'active') as monthly_revenue,
                (SELECT COALESCE(SUM(final_amount), 0) FROM payment_transactions WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as monthly_volume,
                (SELECT COUNT(*) FROM payment_transactions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as monthly_transactions
        ");

        return [
            'total_merchants' => (int) $summary->total_merchants,
            'active_subscriptions' => (int) $summary->active_subscriptions,
            'active_affiliates' => (int) $summary->active_affiliates,
            'monthly_revenue' => (float) $summary->monthly_revenue,
            'monthly_volume' => (float) $summary->monthly_volume,
            'monthly_transactions' => (int) $summary->monthly_transactions,
            'avg_revenue_per_merchant' => $summary->total_merchants > 0 ?
                round($summary->monthly_revenue / $summary->total_merchants, 2) : 0
        ];
    }

    private function getMainKPIs(): array
    {
        $current = DB::selectOne("
            SELECT 
                COUNT(DISTINCT ms.merchant_id) as active_merchants,
                COALESCE(SUM(ms.amount), 0) as mrr,
                COALESCE(AVG(ms.amount), 0) as arpu,
                COUNT(CASE WHEN ms.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_subscriptions_month,
                COUNT(CASE WHEN ms.cancelled_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as churned_month
            FROM merchant_subscriptions ms
            WHERE ms.status = 'active'
        ");

        $previous = DB::selectOne("
            SELECT 
                COUNT(DISTINCT ms.merchant_id) as active_merchants,
                COALESCE(SUM(ms.amount), 0) as mrr
            FROM merchant_subscriptions ms
            WHERE ms.status = 'active'
            AND ms.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");

        $churnRate = $current->active_merchants > 0 ?
            round(($current->churned_month / $current->active_merchants) * 100, 2) : 0;

        $mrrGrowth = $previous->mrr > 0 ?
            round((($current->mrr - $previous->mrr) / $previous->mrr) * 100, 2) : 0;

        return [
            'mrr' => (float) $current->mrr,
            'mrr_growth' => (float) $mrrGrowth,
            'arpu' => (float) $current->arpu,
            'churn_rate' => (float) $churnRate,
            'new_subscriptions' => (int) $current->new_subscriptions_month,
            'active_merchants' => (int) $current->active_merchants
        ];
    }

    private function getAvailableReports(): array
    {
        return [
            [
                'name' => 'Receita',
                'description' => 'Análise detalhada de receita, MRR e projeções',
                'route' => 'admin.reports.revenue',
                'icon' => 'chart-line'
            ],
            [
                'name' => 'Merchants',
                'description' => 'Crescimento, segmentação e análise de churn',
                'route' => 'admin.reports.merchants',
                'icon' => 'users'
            ],
            [
                'name' => 'Afiliados',
                'description' => 'Performance, comissões e ROI do programa',
                'route' => 'admin.reports.affiliates',
                'icon' => 'share-alt'
            ],
            [
                'name' => 'Assinaturas',
                'description' => 'Métricas, cohorts e análise de retenção',
                'route' => 'admin.reports.subscriptions',
                'icon' => 'credit-card'
            ]
        ];
    }

    private function getRevenueData($period, $groupBy): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        $dateFormat = match ($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'quarter' => '%Y-Q%q',
            'year' => '%Y',
            default => '%Y-%m'
        };

        return DB::select("
            SELECT 
                DATE_FORMAT(created_at, ?) as period,
                COUNT(*) as subscriptions,
                SUM(amount) as revenue,
                AVG(amount) as avg_amount
            FROM merchant_subscriptions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND status IN ('active', 'completed')
            GROUP BY DATE_FORMAT(created_at, ?)
            ORDER BY period
        ", [$dateFormat, $interval, $dateFormat]);
    }

    private function getRevenueByPlan($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        return DB::select("
            SELECT 
                plan_name,
                plan_code,
                COUNT(*) as subscriptions,
                SUM(amount) as revenue,
                AVG(amount) as avg_amount,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM merchant_subscriptions WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)), 2) as percentage
            FROM merchant_subscriptions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND status IN ('active', 'completed')
            GROUP BY plan_code, plan_name
            ORDER BY revenue DESC
        ", [$interval, $interval]);
    }

    private function getRevenueByGateway($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        return DB::select("
            SELECT 
                pg.name as gateway_name,
                COUNT(pt.id) as transactions,
                SUM(pt.amount) as volume,
                SUM(CASE WHEN pt.status = 'completed' THEN pt.amount ELSE 0 END) as successful_volume,
                ROUND(
                    COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(pt.id), 0), 2
                ) as success_rate
            FROM payment_gateways pg
            LEFT JOIN payment_transactions pt ON pg.id = pt.gateway_id
                AND pt.created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY pg.id
            ORDER BY successful_volume DESC
        ", [$interval]);
    }

    private function getRevenueProjections(): array
    {
        // Projeção simples baseada no crescimento dos últimos 3 meses
        $growthData = DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(amount) as revenue
            FROM merchant_subscriptions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            AND status IN ('active', 'completed')
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ");

        if (count($growthData) < 3) {
            return [];
        }

        // Calcular tendência de crescimento
        $revenues = array_column($growthData, 'revenue');
        $avgGrowth = 0;

        for ($i = 1; $i < count($revenues); $i++) {
            if ($revenues[$i - 1] > 0) {
                $avgGrowth += (($revenues[$i] - $revenues[$i - 1]) / $revenues[$i - 1]) * 100;
            }
        }

        $avgGrowth = $avgGrowth / (count($revenues) - 1);
        $lastRevenue = end($revenues);

        // Projetar próximos 6 meses
        $projections = [];
        for ($i = 1; $i <= 6; $i++) {
            $projectedRevenue = $lastRevenue * pow(1 + ($avgGrowth / 100), $i);
            $projections[] = [
                'month' => date('Y-m', strtotime("+{$i} month")),
                'projected_revenue' => round($projectedRevenue, 2)
            ];
        }

        return $projections;
    }

    private function getMRRData($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        return DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(CASE WHEN billing_cycle = 'monthly' THEN amount ELSE amount/12 END) as mrr,
                COUNT(*) as active_subscriptions
            FROM merchant_subscriptions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            AND status = 'active'
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ", [$interval]);
    }

    // Implementar outros métodos de relatórios aqui...
    // Por questão de espaço, vou implementar apenas alguns exemplos

    private function getMerchantGrowth($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        return DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_merchants,
                SUM(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) as cumulative_merchants
            FROM merchants
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ", [$interval]);
    }

    private function getAffiliateProgramPerformance($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        return DB::selectOne("
            SELECT 
                COUNT(DISTINCT a.id) as active_affiliates,
                COUNT(DISTINCT ar.id) as total_referrals,
                COUNT(DISTINCT CASE WHEN ar.status = 'converted' THEN ar.id END) as conversions,
                COALESCE(SUM(ac.amount), 0) as total_commissions,
                COALESCE(SUM(CASE WHEN ac.status = 'paid' THEN ac.amount END), 0) as paid_commissions,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN ar.status = 'converted' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(ar.id), 0), 0
                    ), 2
                ) as conversion_rate
            FROM affiliates a
            LEFT JOIN affiliate_referrals ar ON a.id = ar.affiliate_id
                AND ar.created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            LEFT JOIN affiliate_commissions ac ON a.id = ac.affiliate_id
                AND ac.created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            WHERE a.status = 'approved'
        ", [$interval, $interval]);
    }

    private function exportData($data, $type, $format): \Illuminate\Http\Response
    {
        $filename = "{$type}_report_" . date('Y-m-d') . ".{$format}";

        if ($format === 'csv') {
            $output = $this->arrayToCSV($data);
            return Response::make($output, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\""
            ]);
        }

        // Implementar outros formatos (Excel, PDF) conforme necessário
        return Response::json(['error' => 'Formato não suportado'], 400);
    }

    private function arrayToCSV($data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        // Headers
        fputcsv($output, array_keys((array) $data[0]));

        // Data
        foreach ($data as $row) {
            fputcsv($output, (array) $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    // Placeholder methods para completar a implementação
    private function getMerchantPlanSegmentation(): array
    {
        return [];
    }
    private function getMerchantChurnAnalysis($period): array
    {
        return [];
    }
    private function getMerchantLTVData(): array
    {
        return [];
    }
    private function getTopMerchants($period): array
    {
        return [];
    }
    private function getUsageAnalysis(): array
    {
        return [];
    }
    private function getTopAffiliatePerformers($period): array
    {
        return [];
    }
    private function getCommissionAnalysis($period): array
    {
        return [];
    }
    private function getAffiliateConversionData($period): array
    {
        return [];
    }
    private function getAffiliateProgramROI($period): array
    {
        return [];
    }
    private function getAffiliateTrafficSources($period): array
    {
        return [];
    }
    private function getSubscriptionMetrics($period): array
    {
        return [];
    }
    private function getCohortAnalysis(): array
    {
        return [];
    }
    private function getTrialConversionData($period): array
    {
        return [];
    }
    private function getPlanChangeAnalysis($period): array
    {
        return [];
    }
    private function getSeasonalPatterns(): array
    {
        return [];
    }
    private function getRetentionAnalysis($period): array
    {
        return [];
    }
    private function getRevenueExportData($period): array
    {
        return [];
    }
    private function getMerchantsExportData($period): array
    {
        return [];
    }
    private function getAffiliatesExportData($period): array
    {
        return [];
    }
    private function getSubscriptionsExportData($period): array
    {
        return [];
    }
    private function getTransactionsExportData($period): array
    {
        return [];
    }
}
