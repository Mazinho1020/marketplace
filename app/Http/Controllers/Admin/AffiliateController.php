<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Affiliates\Models\Affiliate;
use App\Affiliates\Models\AffiliateCommission;
use App\Affiliates\Models\AffiliateReferral;
use App\Affiliates\Models\AffiliatePayment;
use App\Affiliates\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    private $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Listar todos os affiliates
     */
    public function index(Request $request)
    {
        $query = Affiliate::query()
            ->select([
                'affiliates.*',
                DB::raw('COUNT(DISTINCT ar.id) as total_referrals'),
                DB::raw('COUNT(DISTINCT CASE WHEN ar.status = "converted" THEN ar.id END) as conversions'),
                DB::raw('COALESCE(SUM(ac.amount), 0) as total_commissions'),
                DB::raw('COALESCE(SUM(CASE WHEN ac.status = "pending" THEN ac.amount END), 0) as pending_commissions')
            ])
            ->leftJoin('affiliate_referrals as ar', 'affiliates.id', '=', 'ar.affiliate_id')
            ->leftJoin('affiliate_commissions as ac', 'affiliates.id', '=', 'ac.affiliate_id')
            ->groupBy('affiliates.id');

        // Filtros
        if ($request->has('status')) {
            $query->where('affiliates.status', $request->status);
        }

        if ($request->has('payment_method')) {
            $query->where('affiliates.payment_method', $request->payment_method);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('affiliates.name', 'like', "%{$search}%")
                    ->orWhere('affiliates.email', 'like', "%{$search}%")
                    ->orWhere('affiliates.affiliate_code', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['total_commissions', 'pending_commissions', 'total_referrals', 'conversions'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy("affiliates.{$sortBy}", $sortOrder);
        }

        $affiliates = $query->paginate(20);

        // Estatísticas para filtros
        $stats = $this->getAffiliateStats();

        return view('admin.affiliates.index', compact('affiliates', 'stats'));
    }

    /**
     * Exibir detalhes de um affiliate
     */
    public function show($id)
    {
        $affiliate = Affiliate::findOrFail($id);

        // Estatísticas detalhadas
        $stats = $affiliate->getStatistics();

        // Performance mensal
        $monthlyPerformance = $affiliate->getMonthlyPerformance();

        // Referrals recentes
        $recentReferrals = $this->getRecentReferrals($id);

        // Comissões recentes
        $recentCommissions = $this->getRecentCommissions($id);

        // Pagamentos realizados
        $recentPayments = $this->getRecentPayments($id);

        return view('admin.affiliates.show', compact(
            'affiliate',
            'stats',
            'monthlyPerformance',
            'recentReferrals',
            'recentCommissions',
            'recentPayments'
        ));
    }

    /**
     * Listar comissões de um affiliate
     */
    public function commissions($id, Request $request)
    {
        $affiliate = Affiliate::findOrFail($id);

        $query = AffiliateCommission::where('affiliate_id', $id)
            ->select([
                'affiliate_commissions.*',
                'merchants.business_name as merchant_name',
                'merchant_subscriptions.plan_name',
                'affiliate_referrals.utm_source'
            ])
            ->leftJoin('merchants', 'affiliate_commissions.merchant_id', '=', 'merchants.id')
            ->leftJoin('merchant_subscriptions', 'affiliate_commissions.subscription_id', '=', 'merchant_subscriptions.id')
            ->leftJoin('affiliate_referrals', 'affiliate_commissions.referral_id', '=', 'affiliate_referrals.id');

        // Filtros
        if ($request->has('status')) {
            $query->where('affiliate_commissions.status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('affiliate_commissions.created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('affiliate_commissions.created_at', '<=', $request->date_to);
        }

        $commissions = $query->orderBy('affiliate_commissions.created_at', 'desc')->paginate(20);

        // Estatísticas das comissões
        $commissionStats = $this->getCommissionStats($id);

        return view('admin.affiliates.commissions', compact(
            'affiliate',
            'commissions',
            'commissionStats'
        ));
    }

    /**
     * Listar referrals de um affiliate
     */
    public function referrals($id, Request $request)
    {
        $affiliate = Affiliate::findOrFail($id);

        $query = AffiliateReferral::where('affiliate_id', $id)
            ->select([
                'affiliate_referrals.*',
                'merchants.business_name as merchant_name',
                'merchants.email as merchant_email'
            ])
            ->leftJoin('merchants', 'affiliate_referrals.merchant_id', '=', 'merchants.id');

        // Filtros
        if ($request->has('status')) {
            $query->where('affiliate_referrals.status', $request->status);
        }

        if ($request->has('utm_source')) {
            $query->where('affiliate_referrals.utm_source', $request->utm_source);
        }

        $referrals = $query->orderBy('affiliate_referrals.created_at', 'desc')->paginate(20);

        // Estatísticas dos referrals
        $referralStats = $this->getReferralStats($id);

        // Fontes de tráfego
        $trafficSources = $this->getTrafficSources($id);

        return view('admin.affiliates.referrals', compact(
            'affiliate',
            'referrals',
            'referralStats',
            'trafficSources'
        ));
    }

    /**
     * Listar pagamentos de um affiliate
     */
    public function payments($id, Request $request)
    {
        $affiliate = Affiliate::findOrFail($id);

        $query = AffiliatePayment::where('affiliate_id', $id);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estatísticas dos pagamentos
        $paymentStats = $this->getPaymentStats($id);

        return view('admin.affiliates.payments', compact(
            'affiliate',
            'payments',
            'paymentStats'
        ));
    }

    /**
     * Estatísticas do programa de afiliados
     */
    public function programStatistics()
    {
        $stats = $this->affiliateService->getProgramStatistics();

        // Gráfico de crescimento do programa
        $growthChart = $this->getProgramGrowthChart();

        // Top performers por período
        $topPerformers = [
            '7d' => $this->affiliateService->getTopPerformers(5, '7d'),
            '30d' => $this->affiliateService->getTopPerformers(5, '30d'),
            '90d' => $this->affiliateService->getTopPerformers(5, '90d')
        ];

        // Distribuição por método de pagamento
        $paymentMethodDistribution = $this->getPaymentMethodDistribution();

        // Comissões por status
        $commissionsByStatus = $this->getCommissionsByStatus();

        return view('admin.affiliates.program-statistics', compact(
            'stats',
            'growthChart',
            'topPerformers',
            'paymentMethodDistribution',
            'commissionsByStatus'
        ));
    }

    /**
     * Top performers
     */
    public function topPerformers(Request $request)
    {
        $period = $request->get('period', '30d');
        $limit = $request->get('limit', 20);

        $topPerformers = $this->affiliateService->getTopPerformers($limit, $period);

        // Comparação com período anterior
        $comparison = $this->getPerformanceComparison($period);

        return view('admin.affiliates.top-performers', compact(
            'topPerformers',
            'comparison',
            'period'
        ));
    }

    /**
     * Métodos auxiliares privados
     */

    private function getAffiliateStats(): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
                COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_this_month
            FROM affiliates
        ");

        return [
            'total' => (int) $stats->total,
            'approved' => (int) $stats->approved,
            'pending' => (int) $stats->pending,
            'rejected' => (int) $stats->rejected,
            'suspended' => (int) $stats->suspended,
            'new_month' => (int) $stats->new_this_month
        ];
    }

    private function getRecentReferrals($affiliateId): array
    {
        return DB::select("
            SELECT 
                ar.*,
                m.business_name as merchant_name,
                m.email as merchant_email
            FROM affiliate_referrals ar
            LEFT JOIN merchants m ON ar.merchant_id = m.id
            WHERE ar.affiliate_id = ?
            ORDER BY ar.created_at DESC
            LIMIT 10
        ", [$affiliateId]);
    }

    private function getRecentCommissions($affiliateId): array
    {
        return DB::select("
            SELECT 
                ac.*,
                m.business_name as merchant_name,
                ms.plan_name
            FROM affiliate_commissions ac
            LEFT JOIN merchants m ON ac.merchant_id = m.id
            LEFT JOIN merchant_subscriptions ms ON ac.subscription_id = ms.id
            WHERE ac.affiliate_id = ?
            ORDER BY ac.created_at DESC
            LIMIT 10
        ", [$affiliateId]);
    }

    private function getRecentPayments($affiliateId): array
    {
        return DB::select("
            SELECT *
            FROM affiliate_payments
            WHERE affiliate_id = ?
            ORDER BY created_at DESC
            LIMIT 5
        ", [$affiliateId]);
    }

    private function getCommissionStats($affiliateId): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN amount END), 0) as pending_amount,
                COALESCE(SUM(CASE WHEN status = 'paid' THEN amount END), 0) as paid_amount
            FROM affiliate_commissions
            WHERE affiliate_id = ?
        ", [$affiliateId]);

        return [
            'total' => (int) $stats->total,
            'pending' => (int) $stats->pending,
            'approved' => (int) $stats->approved,
            'paid' => (int) $stats->paid,
            'cancelled' => (int) $stats->cancelled,
            'total_amount' => (float) $stats->total_amount,
            'pending_amount' => (float) $stats->pending_amount,
            'paid_amount' => (float) $stats->paid_amount
        ];
    }

    private function getReferralStats($affiliateId): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN status = 'converted' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 0
                    ), 2
                ) as conversion_rate
            FROM affiliate_referrals
            WHERE affiliate_id = ?
        ", [$affiliateId]);

        return [
            'total' => (int) $stats->total,
            'active' => (int) $stats->active,
            'converted' => (int) $stats->converted,
            'cancelled' => (int) $stats->cancelled,
            'conversion_rate' => (float) $stats->conversion_rate
        ];
    }

    private function getTrafficSources($affiliateId): array
    {
        return DB::select("
            SELECT 
                COALESCE(utm_source, 'Direct') as source,
                COUNT(*) as count,
                COUNT(CASE WHEN status = 'converted' THEN 1 END) as conversions,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN status = 'converted' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 0
                    ), 2
                ) as conversion_rate
            FROM affiliate_referrals
            WHERE affiliate_id = ?
            GROUP BY utm_source
            ORDER BY count DESC
            LIMIT 10
        ", [$affiliateId]);
    }

    private function getPaymentStats($affiliateId): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount END), 0) as paid_amount
            FROM affiliate_payments
            WHERE affiliate_id = ?
        ", [$affiliateId]);

        return [
            'total' => (int) $stats->total,
            'completed' => (int) $stats->completed,
            'pending' => (int) $stats->pending,
            'failed' => (int) $stats->failed,
            'total_amount' => (float) $stats->total_amount,
            'paid_amount' => (float) $stats->paid_amount
        ];
    }

    private function getProgramGrowthChart(): array
    {
        $data = DB::select("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as new_affiliates,
                SUM(COUNT(*)) OVER (ORDER BY DATE_FORMAT(created_at, '%Y-%m')) as total_affiliates
            FROM affiliates
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ");

        $months = [];
        $newAffiliates = [];
        $totalAffiliates = [];

        foreach ($data as $row) {
            $months[] = date('M/Y', strtotime($row->month . '-01'));
            $newAffiliates[] = (int) $row->new_affiliates;
            $totalAffiliates[] = (int) $row->total_affiliates;
        }

        return [
            'months' => $months,
            'new_affiliates' => $newAffiliates,
            'total_affiliates' => $totalAffiliates
        ];
    }

    private function getPaymentMethodDistribution(): array
    {
        return DB::select("
            SELECT 
                payment_method,
                COUNT(*) as count,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM affiliates WHERE payment_method IS NOT NULL), 2) as percentage
            FROM affiliates
            WHERE payment_method IS NOT NULL
            GROUP BY payment_method
            ORDER BY count DESC
        ");
    }

    private function getCommissionsByStatus(): array
    {
        return DB::select("
            SELECT 
                status,
                COUNT(*) as count,
                SUM(amount) as total_amount
            FROM affiliate_commissions
            GROUP BY status
            ORDER BY total_amount DESC
        ");
    }

    private function getPerformanceComparison($period): array
    {
        // Implementar comparação com período anterior
        return [
            'growth_rate' => 15.5,
            'new_conversions' => 45,
            'revenue_increase' => 8.2
        ];
    }
}
