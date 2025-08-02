<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Merchants\Models\Merchant;
use App\Merchants\Models\MerchantSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    /**
     * Listar todos os merchants
     */
    public function index(Request $request)
    {
        $query = Merchant::query()
            ->select([
                'merchants.*',
                'ms.plan_name as current_plan',
                'ms.status as subscription_status',
                'ms.amount as subscription_amount',
                'ms.expires_at as subscription_expires'
            ])
            ->leftJoin('merchant_subscriptions as ms', function ($join) {
                $join->on('merchants.id', '=', 'ms.merchant_id')
                    ->where('ms.status', 'active');
            });

        // Filtros
        if ($request->has('status')) {
            $query->where('ms.status', $request->status);
        }

        if ($request->has('plan')) {
            $query->where('ms.plan_code', $request->plan);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('merchants.business_name', 'like', "%{$search}%")
                    ->orWhere('merchants.email', 'like', "%{$search}%")
                    ->orWhere('merchants.document', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy("merchants.{$sortBy}", $sortOrder);

        $merchants = $query->paginate(20);

        // Estatísticas para filtros
        $stats = $this->getMerchantStats();

        return view('admin.merchants.index', compact('merchants', 'stats'));
    }

    /**
     * Exibir detalhes de um merchant
     */
    public function show($id)
    {
        $merchant = Merchant::with([
            'subscriptions' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        // Estatísticas do merchant
        $stats = $this->getMerchantDetailStats($id);

        // Assinatura atual
        $currentSubscription = $merchant->activeSubscription;

        // Informações de uso
        $usageInfo = $merchant->getUsageInfo();

        // Histórico de transações recentes
        $recentTransactions = $this->getRecentTransactions($id);

        return view('admin.merchants.show', compact(
            'merchant',
            'stats',
            'currentSubscription',
            'usageInfo',
            'recentTransactions'
        ));
    }

    /**
     * Listar assinaturas de um merchant
     */
    public function subscriptions($id)
    {
        $merchant = Merchant::findOrFail($id);

        $subscriptions = MerchantSubscription::where('merchant_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Estatísticas das assinaturas
        $subscriptionStats = $this->getSubscriptionStats($id);

        return view('admin.merchants.subscriptions', compact(
            'merchant',
            'subscriptions',
            'subscriptionStats'
        ));
    }

    /**
     * Listar transações de um merchant
     */
    public function transactions($id)
    {
        $merchant = Merchant::findOrFail($id);

        $transactions = DB::table('payment_transactions as pt')
            ->select([
                'pt.*',
                'pg.name as gateway_name',
                'pg.provider as gateway_type'
            ])
            ->leftJoin('payment_gateways as pg', 'pt.gateway_id', '=', 'pg.id')
            ->where('pt.merchant_id', $id)
            ->orderBy('pt.created_at', 'desc')
            ->paginate(20);

        // Estatísticas das transações
        $transactionStats = $this->getTransactionStats($id);

        return view('admin.merchants.transactions', compact(
            'merchant',
            'transactions',
            'transactionStats'
        ));
    }

    /**
     * Exibir dados de uso de um merchant
     */
    public function usage($id)
    {
        $merchant = Merchant::findOrFail($id);

        // Informações detalhadas de uso
        $usageDetails = $this->getDetailedUsage($id);

        // Histórico de uso por mês
        $usageHistory = $this->getUsageHistory($id);

        // Comparação com limites do plano
        $planComparison = $this->getPlanUsageComparison($id);

        return view('admin.merchants.usage', compact(
            'merchant',
            'usageDetails',
            'usageHistory',
            'planComparison'
        ));
    }

    /**
     * Métodos auxiliares privados
     */

    private function getMerchantStats(): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total_merchants,
                COUNT(CASE WHEN ms.status = 'active' THEN 1 END) as active_subscriptions,
                COUNT(CASE WHEN ms.status = 'trial' THEN 1 END) as trial_subscriptions,
                COUNT(CASE WHEN ms.status = 'cancelled' THEN 1 END) as cancelled_subscriptions,
                COUNT(CASE WHEN merchants.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_this_month
            FROM merchants
            LEFT JOIN merchant_subscriptions ms ON merchants.id = ms.merchant_id AND ms.status IN ('active', 'trial', 'cancelled')
        ");

        return [
            'total' => (int) $stats->total_merchants,
            'active' => (int) $stats->active_subscriptions,
            'trial' => (int) $stats->trial_subscriptions,
            'cancelled' => (int) $stats->cancelled_subscriptions,
            'new_month' => (int) $stats->new_this_month
        ];
    }

    private function getMerchantDetailStats($merchantId): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(ms.id) as total_subscriptions,
                COALESCE(SUM(ms.amount), 0) as total_revenue,
                COUNT(pt.id) as total_transactions,
                COALESCE(SUM(pt.amount), 0) as total_processed,
                DATEDIFF(NOW(), m.created_at) as days_active
            FROM merchants m
            LEFT JOIN merchant_subscriptions ms ON m.id = ms.merchant_id
            LEFT JOIN payment_transactions pt ON m.id = pt.merchant_id AND pt.status = 'completed'
            WHERE m.id = ?
            GROUP BY m.id
        ", [$merchantId]);

        return [
            'total_subscriptions' => (int) ($stats->total_subscriptions ?? 0),
            'total_revenue' => (float) ($stats->total_revenue ?? 0),
            'total_transactions' => (int) ($stats->total_transactions ?? 0),
            'total_processed' => (float) ($stats->total_processed ?? 0),
            'days_active' => (int) ($stats->days_active ?? 0)
        ];
    }

    private function getSubscriptionStats($merchantId): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                COUNT(CASE WHEN status = 'trial' THEN 1 END) as trial,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                COALESCE(AVG(amount), 0) as avg_amount,
                COALESCE(SUM(amount), 0) as total_amount
            FROM merchant_subscriptions
            WHERE merchant_id = ?
        ", [$merchantId]);

        return [
            'total' => (int) $stats->total,
            'active' => (int) $stats->active,
            'trial' => (int) $stats->trial,
            'cancelled' => (int) $stats->cancelled,
            'avg_amount' => (float) $stats->avg_amount,
            'total_amount' => (float) $stats->total_amount
        ];
    }

    private function getTransactionStats($merchantId): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount END), 0) as completed_amount,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 0
                    ), 2
                ) as success_rate
            FROM payment_transactions
            WHERE merchant_id = ?
        ", [$merchantId]);

        return [
            'total' => (int) $stats->total,
            'completed' => (int) $stats->completed,
            'pending' => (int) $stats->pending,
            'failed' => (int) $stats->failed,
            'total_amount' => (float) $stats->total_amount,
            'completed_amount' => (float) $stats->completed_amount,
            'success_rate' => (float) $stats->success_rate
        ];
    }

    private function getRecentTransactions($merchantId): array
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

    private function getDetailedUsage($merchantId): array
    {
        // Aqui você implementaria a lógica específica de uso baseada nas features do merchant
        // Por exemplo: número de usuários, transações processadas, etc.

        return [
            'users' => [
                'current' => 45,
                'limit' => 100,
                'percentage' => 45
            ],
            'transactions' => [
                'current' => 1250,
                'limit' => 5000,
                'percentage' => 25
            ],
            'storage' => [
                'current' => 2.5, // GB
                'limit' => 10, // GB
                'percentage' => 25
            ]
        ];
    }

    private function getUsageHistory($merchantId): array
    {
        // Implementar histórico de uso por mês
        return DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as transactions,
                SUM(amount) as volume
            FROM payment_transactions
            WHERE merchant_id = ?
            AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ", [$merchantId]);
    }

    private function getPlanUsageComparison($merchantId): array
    {
        $merchant = Merchant::find($merchantId);
        $currentPlan = $merchant->activeSubscription;

        if (!$currentPlan) {
            return [];
        }

        return [
            'plan_name' => $currentPlan->plan_name,
            'plan_limits' => $currentPlan->limits ?? [],
            'current_usage' => $merchant->getUsageInfo(),
            'warnings' => [] // Implementar warnings baseados no uso
        ];
    }
}
