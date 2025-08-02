<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentAdminController extends Controller
{
    /**
     * Dashboard principal de pagamentos
     */
    public function dashboard()
    {
        // Dados mock para demonstração
        $stats = [
            'total_transacoes' => 1234,
            'transacoes_aprovadas' => 1050,
            'transacoes_pendentes' => 84,
            'transacoes_rejeitadas' => 100,
            'valor_total' => 156750.89,
            'valor_mes_atual' => 45230.50,
            'gateways_ativos' => 4,
            'webhooks_recebidos' => 23,
        ];

        // Dados mock para gráficos
        $transacoesPorDia = collect([
            ['data' => '2024-12-01', 'total' => 45, 'valor_aprovado' => 4500],
            ['data' => '2024-12-02', 'total' => 52, 'valor_aprovado' => 5200],
            ['data' => '2024-12-03', 'total' => 48, 'valor_aprovado' => 4800],
            ['data' => '2024-12-04', 'total' => 61, 'valor_aprovado' => 6100],
            ['data' => '2024-12-05', 'total' => 55, 'valor_aprovado' => 5500],
            ['data' => '2024-12-06', 'total' => 67, 'valor_aprovado' => 6700],
            ['data' => '2024-12-07', 'total' => 73, 'valor_aprovado' => 7300],
        ]);

        $transacoesPorGateway = collect([
            ['gateway' => 'Mercado Pago', 'total' => 567, 'valor_total' => 67890.50],
            ['gateway' => 'PagSeguro', 'total' => 234, 'valor_total' => 34567.25],
            ['gateway' => 'PicPay', 'total' => 189, 'valor_total' => 23456.78],
            ['gateway' => 'Asaas', 'total' => 98, 'valor_total' => 12345.67],
        ]);

        $transacoesPorMetodo = collect([
            ['metodo' => 'PIX', 'total' => 456, 'percentual' => 42.3],
            ['metodo' => 'Cartão de Crédito', 'total' => 378, 'percentual' => 35.1],
            ['metodo' => 'Boleto', 'total' => 167, 'percentual' => 15.5],
            ['metodo' => 'Cartão de Débito', 'total' => 76, 'percentual' => 7.1],
        ]);

        $transacoesRecentes = collect([
            (object) [
                'id' => 1,
                'external_id' => 'TXN_001',
                'amount' => 150.00,
                'status' => 'approved',
                'payment_method' => 'pix',
                'gateway' => (object) ['name' => 'Mercado Pago'],
                'created_at' => now()->subHours(1)
            ],
            (object) [
                'id' => 2,
                'external_id' => 'TXN_002',
                'amount' => 89.90,
                'status' => 'pending',
                'payment_method' => 'credit_card',
                'gateway' => (object) ['name' => 'PagSeguro'],
                'created_at' => now()->subHours(2)
            ],
            (object) [
                'id' => 3,
                'external_id' => 'TXN_003',
                'amount' => 230.50,
                'status' => 'rejected',
                'payment_method' => 'credit_card',
                'gateway' => (object) ['name' => 'PicPay'],
                'created_at' => now()->subHours(3)
            ]
        ]);

        return view('admin.payments.dashboard', compact(
            'stats',
            'transacoesPorDia',
            'transacoesPorGateway',
            'transacoesPorMetodo',
            'transacoesRecentes'
        ));
    }

    /**
     * Lista de transações
     */
    public function transactions(Request $request)
    {
        // Mock data para demonstração
        $transactions = collect([
            (object) [
                'id' => 1,
                'external_id' => 'TXN_001',
                'amount' => 150.00,
                'status' => 'approved',
                'payment_method' => 'pix',
                'gateway' => (object) ['name' => 'Mercado Pago', 'logo_url' => null],
                'created_at' => now()->subHours(2),
                'customer_name' => 'João Silva',
                'customer_email' => 'joao@email.com',
                'description' => 'Compra de produto teste',
                'payer_email' => 'joao@email.com',
                'payer_name' => 'João Silva',
                'external_url' => null
            ],
            (object) [
                'id' => 2,
                'external_id' => 'TXN_002',
                'amount' => 89.90,
                'status' => 'pending',
                'payment_method' => 'credit_card',
                'gateway' => (object) ['name' => 'PagSeguro', 'logo_url' => null],
                'created_at' => now()->subHours(4),
                'customer_name' => 'Maria Santos',
                'customer_email' => 'maria@email.com',
                'description' => 'Pagamento de serviço',
                'payer_email' => 'maria@email.com',
                'payer_name' => 'Maria Santos',
                'external_url' => null
            ],
            (object) [
                'id' => 3,
                'external_id' => 'TXN_003',
                'amount' => 230.50,
                'status' => 'rejected',
                'payment_method' => 'credit_card',
                'gateway' => (object) ['name' => 'PicPay', 'logo_url' => null],
                'created_at' => now()->subHours(6),
                'customer_name' => 'Pedro Costa',
                'customer_email' => 'pedro@email.com',
                'description' => 'Assinatura mensal cancelada',
                'payer_email' => 'pedro@email.com',
                'payer_name' => 'Pedro Costa',
                'external_url' => null
            ]
        ]);

        // Gateways para o filtro
        $gateways = collect([
            (object) ['id' => 1, 'name' => 'Mercado Pago'],
            (object) ['id' => 2, 'name' => 'PagSeguro'],
            (object) ['id' => 3, 'name' => 'PicPay'],
            (object) ['id' => 4, 'name' => 'Asaas'],
        ]);

        // Métodos de pagamento para o filtro
        $paymentMethods = collect([
            (object) ['value' => 'pix', 'label' => 'PIX'],
            (object) ['value' => 'credit_card', 'label' => 'Cartão de Crédito'],
            (object) ['value' => 'bank_slip', 'label' => 'Boleto Bancário'],
            (object) ['value' => 'debit_card', 'label' => 'Cartão de Débito'],
        ]);

        return view('admin.payments.transactions', compact('transactions', 'gateways', 'paymentMethods'));
    }

    /**
     * Detalhes de uma transação
     */
    public function transactionDetails($id)
    {
        // Mock data
        $transaction = (object) [
            'id' => $id,
            'external_id' => 'TXN_' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'amount' => 150.00,
            'status' => 'approved',
            'payment_method' => 'pix',
            'gateway' => (object) ['name' => 'Mercado Pago', 'logo_url' => null],
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(1),
            'processed_at' => now()->subHours(1),
            'customer_name' => 'João Silva',
            'customer_email' => 'joao@email.com',
            'customer_phone' => '(11) 99999-9999',
            'description' => 'Compra de produto teste',
            'gateway_response' => ['status' => 'approved', 'authorization_code' => 'ABC123'],
            'gateway_data' => ['id' => 'MP_12345', 'status' => 'approved', 'amount' => 150.00],
            'gateway_fee' => 5.95,
            'payer_name' => 'João Silva',
            'payer_email' => 'joao@email.com',
            'payer_document' => '123.456.789-00',
            'payer_phone' => '(11) 99999-9999',
            'external_url' => null
        ];

        return view('admin.payments.transaction-details', compact('transaction'));
    }

    /**
     * Lista de gateways
     */
    public function gateways()
    {
        // Mock data
        $gateways = collect([
            (object) [
                'id' => 1,
                'name' => 'Mercado Pago',
                'code' => 'mercadopago',
                'is_active' => true,
                'total_transactions' => 567,
                'success_rate' => 94.5,
                'total_volume' => 67890.50,
                'logo_url' => null,
                'provider' => 'MercadoLibre Inc.',
                'approved_transactions' => 536,
                'pending_transactions' => 31,
                'approved_amount' => 64245.75,
                'description' => 'Gateway de pagamento líder na América Latina, oferecendo soluções completas para e-commerce.',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(5)
            ],
            (object) [
                'id' => 2,
                'name' => 'PagSeguro',
                'code' => 'pagseguro',
                'is_active' => true,
                'total_transactions' => 234,
                'success_rate' => 91.2,
                'total_volume' => 34567.25,
                'logo_url' => null,
                'provider' => 'PagSeguro Digital Ltd.',
                'approved_transactions' => 213,
                'pending_transactions' => 21,
                'approved_amount' => 31525.35,
                'description' => 'Solução brasileira de pagamentos online com foco em segurança e facilidade de integração.',
                'created_at' => now()->subDays(45),
                'updated_at' => now()->subDays(10)
            ],
            (object) [
                'id' => 3,
                'name' => 'PicPay',
                'code' => 'picpay',
                'is_active' => false,
                'total_transactions' => 189,
                'success_rate' => 88.7,
                'total_volume' => 23456.78,
                'logo_url' => null,
                'provider' => 'PicPay Servicos S.A.',
                'approved_transactions' => 168,
                'pending_transactions' => 21,
                'approved_amount' => 20806.27,
                'description' => 'Carteira digital brasileira com foco em pagamentos via QR Code e transferências instantâneas.',
                'created_at' => now()->subDays(60),
                'updated_at' => now()->subDays(15)
            ]
        ]);

        return view('admin.payments.gateways', compact('gateways'));
    }

    /**
     * Detalhes de um gateway
     */
    public function gatewayDetails($id)
    {
        // Mock data
        $gateway = (object) [
            'id' => $id,
            'name' => 'Mercado Pago',
            'code' => 'mercadopago',
            'is_active' => true,
            'type' => 'gateway',
            'description' => 'Gateway de pagamento do Mercado Pago',
            'fee_fixed' => 0.40,
            'fee_percentage' => 3.99,
            'min_amount' => 1.00,
            'max_amount' => 10000.00,
            'settings' => [
                'environment' => 'production',
                'webhook_url' => 'https://example.com/webhook',
                'timeout' => 30,
                'retry_attempts' => 3
            ]
        ];

        $stats = [
            'total_transactions' => 567,
            'total_volume' => 67890.50,
            'success_rate' => 94.5,
            'avg_transaction' => 119.75
        ];

        $recentTransactions = collect([
            (object) [
                'id' => 1,
                'external_id' => 'TXN_001',
                'amount' => 150.00,
                'status' => 'approved',
                'payment_method' => 'pix',
                'created_at' => now()->subHours(2)
            ]
        ]);

        $recentWebhooks = collect([
            (object) [
                'id' => 1,
                'event_type' => 'payment.approved',
                'status' => 'processed',
                'created_at' => now()->subHours(1)
            ]
        ]);

        $performanceData = [
            'labels' => ['01/12', '02/12', '03/12', '04/12', '05/12'],
            'transactions' => [45, 52, 48, 61, 55],
            'volume' => [4500, 5200, 4800, 6100, 5500]
        ];

        return view('admin.payments.gateway-details', compact(
            'gateway',
            'stats',
            'recentTransactions',
            'recentWebhooks',
            'performanceData'
        ));
    }

    /**
     * Estatísticas por método de pagamento
     */
    public function paymentMethods()
    {
        // Mock data
        $paymentMethodsStats = collect([
            (object) [
                'method' => 'pix',
                'name' => 'PIX',
                'total_transactions' => 456,
                'total_amount' => 45600.00,
                'avg_amount' => 100.00,
                'success_rate' => 98.5
            ],
            (object) [
                'method' => 'credit_card',
                'name' => 'Cartão de Crédito',
                'total_transactions' => 378,
                'total_amount' => 56700.00,
                'avg_amount' => 150.00,
                'success_rate' => 89.2
            ],
            (object) [
                'method' => 'bank_slip',
                'name' => 'Boleto Bancário',
                'total_transactions' => 167,
                'total_amount' => 25050.00,
                'avg_amount' => 150.00,
                'success_rate' => 94.1
            ]
        ]);

        return view('admin.payments.payment-methods', compact('paymentMethodsStats'));
    }

    /**
     * Lista de webhooks
     */
    public function webhooks(Request $request)
    {
        // Mock data
        $webhooks = collect([
            (object) [
                'id' => 1,
                'event_type' => 'payment.approved',
                'status' => 'processed',
                'attempts' => 1,
                'created_at' => now()->subHours(1),
                'processed_at' => now()->subHours(1),
                'transaction' => (object) ['id' => 1, 'external_id' => 'TXN_001', 'amount' => 150.00]
            ],
            (object) [
                'id' => 2,
                'event_type' => 'payment.rejected',
                'status' => 'failed',
                'attempts' => 3,
                'created_at' => now()->subHours(2),
                'processed_at' => null,
                'transaction' => (object) ['id' => 2, 'external_id' => 'TXN_002', 'amount' => 89.90]
            ]
        ]);

        $stats = [
            'total' => 156,
            'processed' => 142,
            'failed' => 8,
            'pending' => 6
        ];

        return view('admin.payments.webhooks', compact('webhooks', 'stats'));
    }

    /**
     * Detalhes de um webhook
     */
    public function webhookDetails($id)
    {
        // Mock data
        $webhook = (object) [
            'id' => $id,
            'event_type' => 'payment.approved',
            'status' => 'processed',
            'attempts' => 1,
            'created_at' => now()->subHours(1),
            'processed_at' => now()->subHours(1),
            'error_message' => null,
            'gateway_signature' => 'sha256=abcd1234...',
            'gateway_data' => [
                'id' => 'MP_12345',
                'status' => 'approved',
                'amount' => 150.00,
                'payment_method' => 'pix'
            ],
            'ip_address' => '192.168.1.1',
            'user_agent' => 'MercadoPago-Webhook/1.0',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-Signature' => 'sha256=abcd1234...'
            ],
            'transaction' => (object) [
                'id' => 1,
                'external_id' => 'TXN_001',
                'amount' => 150.00,
                'status' => 'approved',
                'gateway' => (object) ['name' => 'Mercado Pago']
            ]
        ];

        return view('admin.payments.webhook-details', compact('webhook'));
    }

    /**
     * Configurações de pagamento
     */
    public function settings()
    {
        return view('admin.payments.settings');
    }

    /**
     * Relatórios de pagamento
     */
    public function reports()
    {
        // Mock data para demonstração
        $reportData = [
            'summary' => [
                'total_transactions' => 1088,
                'total_amount' => 134543.95,
                'success_rate' => 92.3,
                'avg_amount' => 123.67
            ],
            'daily_stats' => collect([
                ['date' => '2024-12-01', 'transactions' => 45, 'amount' => 4500.00, 'success_rate' => 91.1],
                ['date' => '2024-12-02', 'transactions' => 52, 'amount' => 5200.00, 'success_rate' => 93.5],
                ['date' => '2024-12-03', 'transactions' => 48, 'amount' => 4800.00, 'success_rate' => 89.6],
                ['date' => '2024-12-04', 'transactions' => 61, 'amount' => 6100.00, 'success_rate' => 94.8],
                ['date' => '2024-12-05', 'transactions' => 55, 'amount' => 5500.00, 'success_rate' => 92.7],
            ]),
            'gateway_stats' => collect([
                ['gateway' => 'Mercado Pago', 'transactions' => 567, 'amount' => 67890.50, 'percentage' => 52.1],
                ['gateway' => 'PagSeguro', 'transactions' => 234, 'amount' => 34567.25, 'percentage' => 21.5],
                ['gateway' => 'PicPay', 'transactions' => 189, 'amount' => 23456.78, 'percentage' => 17.4],
                ['gateway' => 'Asaas', 'transactions' => 98, 'amount' => 12345.67, 'percentage' => 9.0],
            ]),
            'method_stats' => collect([
                ['method' => 'PIX', 'transactions' => 456, 'amount' => 45600.00, 'percentage' => 42.3],
                ['method' => 'Cartão de Crédito', 'transactions' => 378, 'amount' => 56700.00, 'percentage' => 35.1],
                ['method' => 'Boleto', 'transactions' => 167, 'amount' => 25050.00, 'percentage' => 15.5],
                ['method' => 'Cartão de Débito', 'transactions' => 76, 'amount' => 11400.00, 'percentage' => 7.1],
            ])
        ];

        return view('admin.payments.reports', compact('reportData'));
    }
}
