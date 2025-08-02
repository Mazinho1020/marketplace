<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$query = DB::table('payment_transactions as pt')
    ->select([
        'pt.*',
        'pt.final_amount as amount', // Alias para compatibilidade
        'pg.name as gateway_name',
        'pg.provider as gateway_type',
        'pg.id as gateway_id',
        'pg.is_active as gateway_active',
        'm.business_name as merchant_name',
        'm.email as merchant_email'
    ])
    ->leftJoin('payment_gateways as pg', 'pt.gateway_id', '=', 'pg.id')
    ->leftJoin('merchants as m', 'pt.merchant_id', '=', 'm.id');

class PaymentController extends Controller
{
    /**
     * Listar todas as transações
     */
    public function index(Request $request)
    {
        $query = DB::table('payment_transactions as pt')
            ->select([
                'pt.*',
                'pg.name as gateway_name',
                'pg.provider as gateway_type',
                'm.business_name as merchant_name',
                'm.email as merchant_email'
            ])
            ->leftJoin('payment_gateways as pg', 'pt.gateway_id', '=', 'pg.id')
            ->leftJoin('merchants as m', 'pt.merchant_id', '=', 'm.id');

        // Filtros
        if ($request->has('status')) {
            $query->where('pt.status', $request->status);
        }

        if ($request->has('gateway')) {
            $query->where('pt.gateway_id', $request->gateway);
        }

        if ($request->has('payment_method')) {
            $query->where('pt.payment_method', $request->payment_method);
        }

        if ($request->has('date_from')) {
            $query->where('pt.created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('pt.created_at', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pt.external_id', 'like', "%{$search}%")
                    ->orWhere('m.business_name', 'like', "%{$search}%")
                    ->orWhere('m.email', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy("pt.{$sortBy}", $sortOrder);

        $transactions = $query->paginate(25);

        // Estatísticas para filtros
        $stats = $this->getTransactionStats();

        // Gateways disponíveis
        $gateways = DB::table('payment_gateways')->where('is_active', true)->get();

        return view('admin.payments.index', compact('transactions', 'stats', 'gateways'));
    }

    /**
     * Exibir detalhes de uma transação
     */
    public function show($id)
    {
        $transaction = DB::table('payment_transactions as pt')
            ->select([
                'pt.*',
                'pg.name as gateway_name',
                'pg.provider as gateway_type',
                'pg.settings as gateway_config',
                'm.business_name as merchant_name',
                'm.email as merchant_email',
                'm.document as merchant_document'
            ])
            ->leftJoin('payment_gateways as pg', 'pt.gateway_id', '=', 'pg.id')
            ->leftJoin('merchants as m', 'pt.merchant_id', '=', 'm.id')
            ->where('pt.id', $id)
            ->first();

        if (!$transaction) {
            abort(404, 'Transação não encontrada');
        }

        // Histórico de tentativas/webhooks
        $transactionHistory = $this->getTransactionHistory($id);

        // Transações relacionadas (mesmo external_id ou merchant)
        $relatedTransactions = $this->getRelatedTransactions($transaction);

        return view('admin.payments.show', compact(
            'transaction',
            'transactionHistory',
            'relatedTransactions'
        ));
    }

    /**
     * Listar gateways de pagamento
     */
    public function gateways()
    {
        $gateways = DB::table('payment_gateways')->orderBy('name')->get();

        // Estatísticas por gateway
        $gatewayStats = $this->getGatewayStats();

        // Comparação de performance
        $performance = $this->getGatewayPerformance();

        return view('admin.payments.gateways', compact(
            'gateways',
            'gatewayStats',
            'performance'
        ));
    }

    /**
     * Listar transações (view separada)
     */
    public function transactions(Request $request)
    {
        // Query base para transações
        $query = DB::table('payment_transactions as pt')
            ->select([
                'pt.*',
                'pt.final_amount as amount',
                'pt.customer_email as payer_email',
                'pt.customer_name as payer_name',
                'pg.id as gateway_id',
                'pg.name as gateway_name',
                'pg.provider as gateway_type',
                'pg.is_active as gateway_active',
                'm.business_name as merchant_name',
                'm.email as merchant_email',
                DB::raw('NULL as external_url') // Campo placeholder para external_url
            ])
            ->leftJoin('payment_gateways as pg', 'pt.gateway_id', '=', 'pg.id')
            ->leftJoin('merchants as m', 'pt.merchant_id', '=', 'm.id');

        // Aplicar filtros
        if ($request->filled('status')) {
            $query->where('pt.status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('pt.gateway_id', $request->gateway);
        }

        if ($request->filled('payment_method')) {
            $query->where('pt.payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('pt.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('pt.created_at', '<=', $request->date_to);
        }

        // Buscar transações com paginação
        $transactions = collect($query->orderBy('pt.created_at', 'desc')->limit(50)->get());

        // Processar dados para criar objetos gateway compatíveis com a view
        $transactions = $transactions->map(function ($transaction) {
            // Converter datas para objetos Carbon
            if ($transaction->created_at) {
                $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at);
            }
            if ($transaction->updated_at) {
                $transaction->updated_at = \Carbon\Carbon::parse($transaction->updated_at);
            }
            if ($transaction->processed_at) {
                $transaction->processed_at = \Carbon\Carbon::parse($transaction->processed_at);
            }
            if ($transaction->completed_at) {
                $transaction->completed_at = \Carbon\Carbon::parse($transaction->completed_at);
            }
            if ($transaction->cancelled_at) {
                $transaction->cancelled_at = \Carbon\Carbon::parse($transaction->cancelled_at);
            }
            if ($transaction->expires_at) {
                $transaction->expires_at = \Carbon\Carbon::parse($transaction->expires_at);
            }

            // Criar objeto gateway
            if ($transaction->gateway_id) {
                $transaction->gateway = (object) [
                    'id' => $transaction->gateway_id,
                    'name' => $transaction->gateway_name,
                    'provider' => $transaction->gateway_type,
                    'is_active' => $transaction->gateway_active,
                    'logo_url' => null // Pode ser adicionado futuramente
                ];
            } else {
                $transaction->gateway = null;
            }
            return $transaction;
        });

        // Resumo por status
        $statusSummary = $this->getStatusSummary();

        // Volume por período
        $volumeChart = $this->getVolumeChart($request->get('period', '30d'));

        // Métodos de pagamento mais usados
        $paymentMethodStats = $this->getPaymentMethodStats();

        // Lista de métodos para dropdown
        $paymentMethods = [
            (object) ['value' => 'pix', 'label' => 'PIX'],
            (object) ['value' => 'credit_card', 'label' => 'Cartão de Crédito'],
            (object) ['value' => 'debit_card', 'label' => 'Cartão de Débito'],
            (object) ['value' => 'bank_slip', 'label' => 'Boleto'],
            (object) ['value' => 'bank_transfer', 'label' => 'Transferência Bancária'],
        ];

        // Taxa de aprovação por gateway
        $approvalRates = $this->getApprovalRates();

        // Gateways disponíveis para filtros
        $gateways = DB::table('payment_gateways')->where('is_active', true)->orderBy('name')->get();

        return view('admin.payments.transactions', compact(
            'transactions',
            'statusSummary',
            'volumeChart',
            'paymentMethods',
            'paymentMethodStats',
            'approvalRates',
            'gateways'
        ));
    }

    /**
     * Analytics de pagamentos
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '30d');

        // Gráfico de volume de transações
        $volumeChart = $this->getVolumeChart($period);

        // Comparação de gateways
        $gatewayComparison = $this->getGatewayComparison($period);

        // Análise de falhas
        $failureAnalysis = $this->getFailureAnalysis($period);

        // Revenue tracking
        $revenueTracking = $this->getRevenueTracking($period);

        // Tempo médio de processamento
        $processingTimes = $this->getProcessingTimes($period);

        return view('admin.payments.analytics', compact(
            'volumeChart',
            'gatewayComparison',
            'failureAnalysis',
            'revenueTracking',
            'processingTimes',
            'period'
        ));
    }

    /**
     * Métodos auxiliares privados
     */

    private function getTransactionStats(): array
    {
        $stats = DB::selectOne("
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed,
                COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                COALESCE(SUM(final_amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN status = 'completed' THEN final_amount END), 0) as completed_amount,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 0
                    ), 2
                ) as success_rate
            FROM payment_transactions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");

        return [
            'total' => (int) $stats->total,
            'completed' => (int) $stats->completed,
            'pending' => (int) $stats->pending,
            'failed' => (int) $stats->failed,
            'cancelled' => (int) $stats->cancelled,
            'total_amount' => (float) $stats->total_amount,
            'completed_amount' => (float) $stats->completed_amount,
            'success_rate' => (float) $stats->success_rate
        ];
    }

    private function getTransactionHistory($transactionId): array
    {
        // Implementar sistema de logs/auditoria de transações
        // Por enquanto, simulando com status changes
        return [
            [
                'event' => 'created',
                'timestamp' => '2024-01-15 10:30:00',
                'description' => 'Transação criada',
                'details' => []
            ],
            [
                'event' => 'processing',
                'timestamp' => '2024-01-15 10:30:15',
                'description' => 'Enviado para gateway',
                'details' => ['gateway' => 'Safe2Pay']
            ],
            [
                'event' => 'completed',
                'timestamp' => '2024-01-15 10:31:02',
                'description' => 'Pagamento aprovado',
                'details' => ['authorization_code' => 'ABC123']
            ]
        ];
    }

    private function getRelatedTransactions($transaction): array
    {
        return DB::select("
            SELECT 
                pt.*,
                pg.name as gateway_name,
                m.business_name as merchant_name
            FROM payment_transactions pt
            LEFT JOIN payment_gateways pg ON pt.gateway_id = pg.id
            LEFT JOIN merchants m ON pt.merchant_id = m.id
            WHERE (pt.external_id = ? OR pt.merchant_id = ?)
            AND pt.id != ?
            ORDER BY pt.created_at DESC
            LIMIT 10
        ", [$transaction->external_id, $transaction->merchant_id, $transaction->id]);
    }

    private function getGatewayStats(): array
    {
        return DB::select("
            SELECT 
                pg.id,
                pg.name,
                pg.provider as type,
                pg.is_active,
                COUNT(pt.id) as total_transactions,
                COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) as successful_transactions,
                COALESCE(SUM(pt.amount), 0) as total_volume,
                COALESCE(SUM(CASE WHEN pt.status = 'completed' THEN pt.amount END), 0) as successful_volume,
                ROUND(
                    COALESCE(
                        COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(pt.id), 0), 0
                    ), 2
                ) as success_rate
            FROM payment_gateways pg
            LEFT JOIN payment_transactions pt ON pg.id = pt.gateway_id
                AND pt.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY pg.id
            ORDER BY total_volume DESC
        ");
    }

    private function getGatewayPerformance(): array
    {
        return DB::select("
            SELECT 
                pg.name,
                COUNT(pt.id) as transactions,
                ROUND(AVG(CASE WHEN pt.status = 'completed' THEN 
                    TIMESTAMPDIFF(SECOND, pt.created_at, pt.updated_at) END), 2) as avg_processing_time,
                ROUND(
                    COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(pt.id), 0), 2
                ) as success_rate,
                SUM(CASE WHEN pt.status = 'completed' THEN pt.amount ELSE 0 END) as volume
            FROM payment_gateways pg
            LEFT JOIN payment_transactions pt ON pg.id = pt.gateway_id
                AND pt.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            WHERE pg.is_active = 1
            GROUP BY pg.id
            ORDER BY success_rate DESC
        ");
    }

    private function getStatusSummary(): array
    {
        return DB::select("
            SELECT 
                status,
                COUNT(*) as count,
                SUM(final_amount) as total_amount,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM payment_transactions), 2) as percentage
            FROM payment_transactions
            GROUP BY status
            ORDER BY count DESC
        ");
    }

    private function getVolumeChart($period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };

        $data = DB::select("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as transactions,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as successful,
                SUM(final_amount) as volume,
                SUM(CASE WHEN status = 'completed' THEN final_amount ELSE 0 END) as successful_volume
            FROM payment_transactions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$interval]);

        $dates = [];
        $transactions = [];
        $successful = [];
        $volumes = [];

        foreach ($data as $row) {
            $dates[] = date('d/m', strtotime($row->date));
            $transactions[] = (int) $row->transactions;
            $successful[] = (int) $row->successful;
            $volumes[] = (float) $row->volume;
        }

        return [
            'dates' => $dates,
            'transactions' => $transactions,
            'successful' => $successful,
            'volumes' => $volumes
        ];
    }

    private function getPaymentMethodStats(): array
    {
        return DB::select("
            SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(final_amount) as volume,
                ROUND(
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(*), 0), 2
                ) as success_rate,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM payment_transactions), 2) as percentage
            FROM payment_transactions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY payment_method
            ORDER BY count DESC
        ");
    }

    private function getApprovalRates(): array
    {
        return DB::select("
            SELECT 
                pg.name as gateway_name,
                payment_method,
                COUNT(*) as total_transactions,
                COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) as approved_transactions,
                ROUND(
                    COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(*), 0), 2
                ) as approval_rate
            FROM payment_transactions pt
            LEFT JOIN payment_gateways pg ON pt.gateway_id = pg.id
            WHERE pt.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY pg.id, payment_method
            HAVING total_transactions >= 10
            ORDER BY approval_rate DESC
        ");
    }

    private function getGatewayComparison($period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };

        return DB::select("
            SELECT 
                pg.name,
                COUNT(pt.id) as transactions,
                SUM(pt.amount) as volume,
                ROUND(
                    COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) * 100.0 / 
                    NULLIF(COUNT(pt.id), 0), 2
                ) as success_rate,
                ROUND(AVG(CASE WHEN pt.status = 'completed' THEN 
                    TIMESTAMPDIFF(SECOND, pt.created_at, pt.updated_at) END), 2) as avg_processing_time
            FROM payment_gateways pg
            LEFT JOIN payment_transactions pt ON pg.id = pt.gateway_id
                AND pt.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            WHERE pg.is_active = 1
            GROUP BY pg.id
            ORDER BY volume DESC
        ", [$interval]);
    }

    private function getFailureAnalysis($period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };

        return [
            'by_gateway' => DB::select("
                SELECT 
                    pg.name,
                    COUNT(CASE WHEN pt.status = 'failed' THEN 1 END) as failed_count,
                    COUNT(*) as total_count,
                    ROUND(
                        COUNT(CASE WHEN pt.status = 'failed' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 2
                    ) as failure_rate
                FROM payment_gateways pg
                LEFT JOIN payment_transactions pt ON pg.id = pt.gateway_id
                    AND pt.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY pg.id
                HAVING total_count > 0
                ORDER BY failure_rate DESC
            ", [$interval]),

            'by_method' => DB::select("
                SELECT 
                    payment_method,
                    COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_count,
                    COUNT(*) as total_count,
                    ROUND(
                        COUNT(CASE WHEN status = 'failed' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(*), 0), 2
                    ) as failure_rate
                FROM payment_transactions
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY payment_method
                ORDER BY failure_rate DESC
            ", [$interval])
        ];
    }

    private function getRevenueTracking($period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };

        return DB::select("
            SELECT 
                DATE(created_at) as date,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as revenue,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as successful_transactions
            FROM payment_transactions
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ", [$interval]);
    }

    private function getProcessingTimes($period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 30
        };

        return DB::select("
            SELECT 
                pg.name as gateway_name,
                payment_method,
                COUNT(*) as transactions,
                ROUND(AVG(TIMESTAMPDIFF(SECOND, pt.created_at, pt.updated_at)), 2) as avg_time_seconds,
                ROUND(MIN(TIMESTAMPDIFF(SECOND, pt.created_at, pt.updated_at)), 2) as min_time_seconds,
                ROUND(MAX(TIMESTAMPDIFF(SECOND, pt.created_at, pt.updated_at)), 2) as max_time_seconds
            FROM payment_transactions pt
            LEFT JOIN payment_gateways pg ON pt.gateway_id = pg.id
            WHERE pt.status = 'completed'
            AND pt.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY pg.id, payment_method
            HAVING transactions >= 5
            ORDER BY avg_time_seconds
        ", [$interval]);
    }

    /**
     * Métodos de pagamento disponíveis
     */
    public function methods()
    {
        $paymentMethodsStats = collect(DB::select("
            SELECT 
                payment_method as method,
                COUNT(*) as total_transactions,
                COALESCE(SUM(final_amount), 0) as total_amount,
                COALESCE(AVG(final_amount), 0) as avg_amount,
                ROUND(
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / COUNT(*), 2
                ) as success_rate
            FROM payment_transactions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY payment_method
            ORDER BY total_transactions DESC
        "));

        return view('admin.payments.payment-methods', compact('paymentMethodsStats'));
    }

    /**
     * Webhooks de pagamento
     */
    public function webhooks()
    {
        $webhooks = collect(DB::select("
            SELECT 
                pg.name as gateway_name,
                pg.webhook_url,
                pg.is_active,
                COUNT(pt.id) as total_transactions,
                COUNT(CASE WHEN pt.status = 'completed' THEN 1 END) as successful_transactions
            FROM payment_gateways pg
            LEFT JOIN payment_transactions pt ON pt.gateway_id = pg.id
            WHERE pg.webhook_url IS NOT NULL
            GROUP BY pg.id, pg.name, pg.webhook_url, pg.is_active
            ORDER BY total_transactions DESC
        "));

        return view('admin.payments.webhooks', compact('webhooks'));
    }

    /**
     * Relatórios de pagamento
     */
    public function reports()
    {
        $dailyStats = DB::select("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as transactions,
                COALESCE(SUM(final_amount), 0) as revenue,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as successful,
                COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed
            FROM payment_transactions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
            LIMIT 30
        ");

        $methodStats = DB::select("
            SELECT 
                payment_method,
                COUNT(*) as count,
                COALESCE(SUM(final_amount), 0) as total
            FROM payment_transactions 
            WHERE status = 'completed' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY payment_method
            ORDER BY count DESC
        ");

        return view('admin.payments.reports', compact('dailyStats', 'methodStats'));
    }

    /**
     * Configurações de pagamento
     */
    public function settings()
    {
        $gateways = DB::select("
            SELECT 
                id,
                name,
                provider,
                environment,
                is_active,
                created_at
            FROM payment_gateways
            ORDER BY name
        ");

        $globalStats = DB::selectOne("
            SELECT 
                COUNT(*) as total_transactions,
                COALESCE(SUM(final_amount), 0) as total_volume,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as successful_transactions,
                ROUND(
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / COUNT(*), 2
                ) as success_rate
            FROM payment_transactions
        ");

        return view('admin.payments.settings', compact('gateways', 'globalStats'));
    }

    /**
     * Detalhes de uma transação específica
     */
    public function transactionDetails($id)
    {
        $transaction = DB::table('payment_transactions as pt')
            ->select([
                'pt.*',
                'pt.final_amount as amount',
                'pt.customer_email as payer_email',
                'pt.customer_name as payer_name',
                'pg.id as gateway_id',
                'pg.name as gateway_name',
                'pg.provider as gateway_type',
                'pg.is_active as gateway_active',
                'm.business_name as merchant_name',
                'm.email as merchant_email'
            ])
            ->leftJoin('payment_gateways as pg', 'pt.gateway_id', '=', 'pg.id')
            ->leftJoin('merchants as m', 'pt.merchant_id', '=', 'm.id')
            ->where('pt.id', $id)
            ->first();

        if (!$transaction) {
            abort(404, 'Transação não encontrada');
        }

        // Converter datas para Carbon
        if ($transaction->created_at) {
            $transaction->created_at = \Carbon\Carbon::parse($transaction->created_at);
        }
        if ($transaction->updated_at) {
            $transaction->updated_at = \Carbon\Carbon::parse($transaction->updated_at);
        }

        // Criar objeto gateway
        $transaction->gateway = (object) [
            'id' => $transaction->gateway_id,
            'name' => $transaction->gateway_name,
            'provider' => $transaction->gateway_type,
            'is_active' => $transaction->gateway_active
        ];

        return view('admin.payments.transaction-details', compact('transaction'));
    }
}
