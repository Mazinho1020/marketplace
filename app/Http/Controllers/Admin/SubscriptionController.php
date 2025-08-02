<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Merchants\Models\MerchantSubscription;
use App\Merchants\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Listar todas as assinaturas
     */
    public function index(Request $request)
    {
        $query = MerchantSubscription::query()
            ->select([
                'merchant_subscriptions.*',
                'merchants.business_name as merchant_name',
                'merchants.email as merchant_email'
            ])
            ->leftJoin('merchants', 'merchant_subscriptions.merchant_id', '=', 'merchants.id');

        // Filtros
        if ($request->has('status')) {
            $query->where('merchant_subscriptions.status', $request->status);
        }

        if ($request->has('plan')) {
            $query->where('merchant_subscriptions.plan_code', $request->plan);
        }

        if ($request->has('billing_cycle')) {
            $query->where('merchant_subscriptions.billing_cycle', $request->billing_cycle);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('merchants.business_name', 'like', "%{$search}%")
                    ->orWhere('merchants.email', 'like', "%{$search}%")
                    ->orWhere('merchant_subscriptions.plan_name', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy("merchant_subscriptions.{$sortBy}", $sortOrder);

        $subscriptions = $query->paginate(20);

        // Estatísticas para filtros
        $stats = $this->getSubscriptionStats();

        // Planos disponíveis para filtro
        $plans = SubscriptionPlan::all();

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * Exibir detalhes de uma assinatura
     */
    public function show($id)
    {
        $subscription = MerchantSubscription::with([
            'merchant',
            'plan'
        ])->findOrFail($id);

        // Histórico de mudanças da assinatura
        $history = $this->getSubscriptionHistory($id);

        // Transações relacionadas
        $relatedTransactions = $this->getRelatedTransactions($subscription->merchant_id);

        // Estatísticas de uso
        $usageStats = $this->getUsageStats($subscription->merchant_id);

        return view('admin.subscriptions.show', compact(
            'subscription',
            'history',
            'relatedTransactions',
            'usageStats'
        ));
    }

    /**
     * Comparação de planos
     */
    public function plansComparison()
    {
        $plans = SubscriptionPlan::orderBy('price_monthly')->get();

        // Estatísticas por plano
        $planStats = $this->getPlanStats();

        // Distribuição de assinaturas por plano
        $planDistribution = $this->getPlanDistribution();

        // Revenue por plano
        $planRevenue = $this->getPlanRevenue();

        return view('admin.subscriptions.plans-comparison', compact(
            'plans',
            'planStats',
            'planDistribution',
            'planRevenue'
        ));
    }

    /**
     * Analytics de assinaturas
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '12m');

        // Gráfico de crescimento de assinaturas
        $growthChart = $this->getGrowthChart($period);

        // Churn rate por período
        $churnData = $this->getChurnData($period);

        // Revenue por ciclo de cobrança
        $revenueByBilling = $this->getRevenueByBilling();

        // Conversion rate de trials
        $trialConversion = $this->getTrialConversion();

        // Métricas de upgrade/downgrade
        $planChanges = $this->getPlanChanges($period);

        return view('admin.subscriptions.analytics', compact(
            'growthChart',
            'churnData',
            'revenueByBilling',
            'trialConversion',
            'planChanges',
            'period'
        ));
    }

    /**
     * Métodos auxiliares privados
     */

    private function getSubscriptionStats(): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                COUNT(CASE WHEN status = 'trial' THEN 1 END) as trial,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired,
                COALESCE(SUM(CASE WHEN status = 'active' THEN amount END), 0) as monthly_revenue,
                COALESCE(AVG(amount), 0) as avg_amount
            FROM merchant_subscriptions
        ");

        return [
            'total' => (int) $stats->total,
            'active' => (int) $stats->active,
            'trial' => (int) $stats->trial,
            'cancelled' => (int) $stats->cancelled,
            'expired' => (int) $stats->expired,
            'monthly_revenue' => (float) $stats->monthly_revenue,
            'avg_amount' => (float) $stats->avg_amount
        ];
    }

    private function getSubscriptionHistory($subscriptionId): array
    {
        // Aqui você implementaria um sistema de auditoria/histórico
        // Por enquanto, vamos simular com mudanças de status
        return DB::select("
            SELECT 
                'status_change' as event_type,
                status as new_value,
                created_at as event_date,
                'Sistema' as changed_by
            FROM merchant_subscriptions
            WHERE id = ?
            ORDER BY created_at DESC
        ", [$subscriptionId]);
    }

    private function getRelatedTransactions($merchantId): array
    {
        return DB::select("
            SELECT 
                pt.id,
                pt.external_id,
                pt.amount,
                pt.status,
                pt.payment_method,
                pt.created_at,
                pg.name as gateway_name
            FROM payment_transactions pt
            LEFT JOIN payment_gateways pg ON pt.gateway_id = pg.id
            WHERE pt.merchant_id = ?
            ORDER BY pt.created_at DESC
            LIMIT 10
        ", [$merchantId]);
    }

    private function getUsageStats($merchantId): array
    {
        // Implementar estatísticas de uso baseadas nas features
        return [
            'transactions_month' => 450,
            'users_active' => 25,
            'storage_used' => 1.2, // GB
            'api_calls' => 15000
        ];
    }

    private function getPlanStats(): array
    {
        return DB::select("
            SELECT 
                sp.name,
                sp.code,
                sp.price_monthly,
                sp.price_yearly,
                COUNT(ms.id) as active_subscriptions,
                COALESCE(SUM(ms.amount), 0) as total_revenue,
                COALESCE(AVG(ms.amount), 0) as avg_revenue
            FROM subscription_plans sp
            LEFT JOIN merchant_subscriptions ms ON sp.code = ms.plan_code AND ms.status = 'active'
            GROUP BY sp.id
            ORDER BY sp.price_monthly
        ");
    }

    private function getPlanDistribution(): array
    {
        return DB::select("
            SELECT 
                plan_code,
                plan_name,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM merchant_subscriptions WHERE status = 'active'), 2) as percentage
            FROM merchant_subscriptions
            WHERE status = 'active'
            GROUP BY plan_code, plan_name
            ORDER BY count DESC
        ");
    }

    private function getPlanRevenue(): array
    {
        return DB::select("
            SELECT 
                plan_code,
                plan_name,
                billing_cycle,
                COUNT(*) as subscriptions,
                SUM(amount) as revenue
            FROM merchant_subscriptions
            WHERE status = 'active'
            GROUP BY plan_code, plan_name, billing_cycle
            ORDER BY revenue DESC
        ");
    }

    private function getGrowthChart($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        $data = DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_subscriptions,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active_subscriptions,
                SUM(amount) as revenue
            FROM merchant_subscriptions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ", [$interval]);

        $months = [];
        $newSubs = [];
        $activeSubs = [];
        $revenues = [];

        foreach ($data as $row) {
            $months[] = date('M/Y', strtotime($row->month . '-01'));
            $newSubs[] = (int) $row->new_subscriptions;
            $activeSubs[] = (int) $row->active_subscriptions;
            $revenues[] = (float) $row->revenue;
        }

        return [
            'months' => $months,
            'new_subscriptions' => $newSubs,
            'active_subscriptions' => $activeSubs,
            'revenues' => $revenues
        ];
    }

    private function getChurnData($period): array
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
                DATE_FORMAT(cancelled_at, '%Y-%m') as month,
                COUNT(*) as churned_subscriptions,
                AVG(DATEDIFF(cancelled_at, started_at)) as avg_lifetime_days
            FROM merchant_subscriptions
            WHERE cancelled_at IS NOT NULL
            AND cancelled_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(cancelled_at, '%Y-%m')
            ORDER BY month
        ", [$interval]);
    }

    private function getRevenueByBilling(): array
    {
        return DB::select("
            SELECT 
                billing_cycle,
                COUNT(*) as subscriptions,
                SUM(amount) as revenue,
                AVG(amount) as avg_amount
            FROM merchant_subscriptions
            WHERE status = 'active'
            GROUP BY billing_cycle
        ");
    }

    private function getTrialConversion(): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total_trials,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as converted_trials,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN status = 'active' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 0
                    ), 2
                ) as conversion_rate,
                AVG(DATEDIFF(started_at, trial_ends_at)) as avg_trial_duration
            FROM merchant_subscriptions
            WHERE trial_ends_at IS NOT NULL
        ");

        return [
            'total_trials' => (int) ($stats->total_trials ?? 0),
            'converted_trials' => (int) ($stats->converted_trials ?? 0),
            'conversion_rate' => (float) ($stats->conversion_rate ?? 0),
            'avg_trial_duration' => (int) ($stats->avg_trial_duration ?? 0)
        ];
    }

    private function getPlanChanges($period): array
    {
        $interval = match ($period) {
            '3m' => 3,
            '6m' => 6,
            '12m' => 12,
            '24m' => 24,
            default => 12
        };

        // Implementar lógica de tracking de mudanças de plano
        // Por enquanto, simulação baseada em criação de subscriptions
        return [
            'upgrades' => 25,
            'downgrades' => 8,
            'net_upgrades' => 17
        ];
    }
}
