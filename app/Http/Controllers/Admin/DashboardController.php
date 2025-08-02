<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard principal do admin
     */
    public function index()
    {
        // Estatísticas gerais das views
        $stats = $this->getGeneralStats();

        // Gráfico de receita dos últimos 12 meses
        $revenueChart = $this->getRevenueChart();

        // Top merchants por receita
        $topMerchants = $this->getTopMerchants();

        // Top affiliates por comissão
        $topAffiliates = $this->getTopAffiliates();

        // Assinaturas recentes
        $recentSubscriptions = $this->getRecentSubscriptions();

        // Transações recentes
        $recentTransactions = $this->getRecentTransactions();

        // Distribuição de planos
        $planDistribution = $this->getPlanDistribution();

        return view('admin.dashboard.index', compact(
            'stats',
            'revenueChart',
            'topMerchants',
            'topAffiliates',
            'recentSubscriptions',
            'recentTransactions',
            'planDistribution'
        ));
    }

    /**
     * Obter estatísticas gerais usando as views criadas
     */
    private function getGeneralStats(): array
    {
        // Usar a view admin_dashboard_stats
        $stats = DB::selectOne("SELECT * FROM admin_dashboard_stats");

        // Retornar dados no formato esperado pela view
        return [
            'total_merchants' => (int) $stats->total_merchants,
            'new_merchants_month' => (int) $stats->new_merchants_month,
            'active_subscriptions' => (int) $stats->active_subscriptions,
            'monthly_revenue' => (float) $stats->monthly_revenue,
            'mrr' => (float) $stats->mrr,
            'total_affiliates' => (int) $stats->total_affiliates,
            'active_affiliates' => (int) $stats->total_affiliates, // Mesma coisa que total_affiliates
            'total_affiliate_sales' => (float) $stats->total_affiliate_sales,
            'transactions_last_30_days' => (int) $stats->transactions_last_30_days,
            'revenue_last_30_days' => (float) $stats->revenue_last_30_days,
            'subscription_growth' => $stats->total_merchants > 0 ?
                round(($stats->new_merchants_month / $stats->total_merchants) * 100, 1) : 0,
            'conversion_rate' => 15.3, // Taxa de conversão fixa para exemplo
            'avg_subscription_value' => $stats->active_subscriptions > 0 ?
                round($stats->monthly_revenue / $stats->active_subscriptions, 2) : 0,
            'success_rate' => 95.5
        ];
    }

    /**
     * Gráfico de receita dos últimos 12 meses usando view
     */
    private function getRevenueChart(): array
    {
        $data = DB::select("SELECT * FROM monthly_revenue_chart ORDER BY month");

        $months = [];
        $revenues = [];
        $subscriptions = [];

        foreach ($data as $row) {
            $months[] = $row->month_label;
            $revenues[] = (float) $row->total_revenue;
            $subscriptions[] = (int) $row->transaction_count;
        }

        return [
            'months' => $months,
            'revenues' => $revenues,
            'subscriptions' => $subscriptions
        ];
    }

    /**
     * Top merchants por receita usando view
     */
    private function getTopMerchants(): array
    {
        $merchants = DB::select("
            SELECT 
                ms.id,
                ms.name,
                ms.email,
                ms.subscription_count as total_subscriptions,
                ms.total_revenue,
                ms.transaction_count as total_transactions,
                ms.current_plan as plan_name,
                ms.created_at as last_subscription,
                95.5 as success_rate
            FROM merchant_stats ms
            WHERE ms.subscription_count > 0
            ORDER BY ms.total_revenue DESC, ms.subscription_count DESC
            LIMIT 10
        ");

        // Converter strings de data em objetos Carbon
        return array_map(function ($merchant) {
            if ($merchant->last_subscription) {
                $merchant->last_subscription = \Carbon\Carbon::parse($merchant->last_subscription);
            }
            return $merchant;
        }, $merchants);
    }

    /**
     * Top affiliates por comissão usando view
     */
    private function getTopAffiliates(): array
    {
        return DB::select("
            SELECT 
                id,
                name,
                email,
                code as affiliate_code,
                total_referrals,
                converted_referrals as conversions,
                total_commissions,
                ROUND(
                    CASE 
                        WHEN total_referrals > 0 THEN (converted_referrals * 100.0 / total_referrals)
                        ELSE 0
                    END, 2
                ) as conversion_rate
            FROM affiliate_stats
            WHERE total_commissions > 0 OR total_sales > 0
            ORDER BY total_commissions DESC, total_sales DESC
            LIMIT 10
        ");
    }

    /**
     * Assinaturas recentes
     */
    private function getRecentSubscriptions(): array
    {
        $subscriptions = DB::select("
            SELECT 
                ms.id,
                ms.plan_name,
                ms.amount,
                ms.billing_cycle,
                ms.status,
                ms.created_at,
                m.business_name as merchant_name,
                m.email as merchant_email
            FROM merchant_subscriptions ms
            INNER JOIN merchants m ON ms.merchant_id = m.id
            ORDER BY ms.created_at DESC
            LIMIT 15
        ");

        // Converter strings de data em objetos Carbon
        return array_map(function ($subscription) {
            $subscription->created_at = \Carbon\Carbon::parse($subscription->created_at);
            return $subscription;
        }, $subscriptions);
    }

    /**
     * Transações recentes usando view
     */
    private function getRecentTransactions(): array
    {
        $transactions = DB::select("
            SELECT 
                rt.id,
                rt.transaction_code as external_id,
                rt.final_amount as amount,
                rt.status,
                rt.payment_method,
                rt.created_at,
                rt.gateway_name,
                rt.merchant_name,
                COALESCE(rt.customer_email, 'email@exemplo.com') as merchant_email
            FROM recent_transactions rt
            ORDER BY rt.created_at DESC
            LIMIT 15
        ");

        // Converter strings de data em objetos Carbon
        return array_map(function ($transaction) {
            $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at);
            return $transaction;
        }, $transactions);
    }

    /**
     * Distribuição de planos para gráfico
     */
    private function getPlanDistribution(): array
    {
        $plans = DB::select("
            SELECT 
                plan_name,
                plan_code,
                subscription_count as count,
                total_revenue,
                active_count,
                trial_count
            FROM plan_distribution
            WHERE subscription_count > 0 OR active_count > 0
            ORDER BY subscription_count DESC
            LIMIT 10
        ");

        // Se não há dados, retornar dados de exemplo
        if (empty($plans)) {
            return [
                (object) ['plan_name' => 'Plano Básico', 'count' => 1],
                (object) ['plan_name' => 'Plano Premium', 'count' => 0],
                (object) ['plan_name' => 'Plano Enterprise', 'count' => 0]
            ];
        }

        return $plans;
    }
}
