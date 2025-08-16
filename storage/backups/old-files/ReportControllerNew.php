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

        // Relatórios disponíveis
        $availableReports = $this->getAvailableReports();

        return view('admin.reports.index', compact(
            'executiveSummary',
            'availableReports'
        ));
    }

    /**
     * Relatório de receita
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', '12m');

        // Receita por período
        $revenueData = $this->getRevenueData($period);

        // Receita por gateway
        $revenueByGateway = $this->getRevenueByGateway($period);

        return view('admin.reports.revenue', compact(
            'revenueData',
            'revenueByGateway',
            'period'
        ));
    }

    /**
     * Exportar relatórios
     */
    public function export(Request $request, $type)
    {
        switch ($type) {
            case 'revenue':
                return $this->exportRevenue($request);
            case 'transactions':
                return $this->exportTransactions($request);
            default:
                abort(404);
        }
    }

    /**
     * Obter resumo executivo
     */
    private function getExecutiveSummary(): array
    {
        try {
            $stats = DB::selectOne("
                SELECT 
                    COUNT(*) as total_transacoes,
                    COUNT(CASE WHEN status = 'aprovada' THEN 1 END) as aprovadas,
                    COALESCE(SUM(CASE WHEN status = 'aprovada' THEN valor_final END), 0) as receita_total,
                    COUNT(DISTINCT empresa_id) as empresas_ativas,
                    COUNT(DISTINCT gateway_id) as gateways_usados
                FROM afi_plan_transacoes
                WHERE data_transacao >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");

            return [
                'total_transacoes' => (int) $stats->total_transacoes,
                'aprovadas' => (int) $stats->aprovadas,
                'receita_total' => (float) $stats->receita_total,
                'empresas_ativas' => (int) $stats->empresas_ativas,
                'gateways_usados' => (int) $stats->gateways_usados,
                'taxa_aprovacao' => $stats->total_transacoes > 0 ?
                    round(($stats->aprovadas / $stats->total_transacoes) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total_transacoes' => 0,
                'aprovadas' => 0,
                'receita_total' => 0,
                'empresas_ativas' => 0,
                'gateways_usados' => 0,
                'taxa_aprovacao' => 0
            ];
        }
    }

    /**
     * Relatórios disponíveis
     */
    private function getAvailableReports(): array
    {
        return [
            [
                'title' => 'Relatório de Receita',
                'description' => 'Análise detalhada da receita por período e gateway',
                'route' => 'admin.reports.revenue',
                'icon' => 'uil-chart-line'
            ],
            [
                'title' => 'Exportar Transações',
                'description' => 'Exportar dados de transações em CSV',
                'route' => 'admin.reports.export',
                'params' => ['type' => 'transactions'],
                'icon' => 'uil-download-alt'
            ]
        ];
    }

    /**
     * Dados de receita por período
     */
    private function getRevenueData(string $period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '12m' => 365,
            default => 30
        };

        try {
            $data = DB::select("
                SELECT 
                    DATE(data_transacao) as date,
                    COALESCE(SUM(CASE WHEN status = 'aprovada' THEN valor_final END), 0) as revenue,
                    COUNT(CASE WHEN status = 'aprovada' THEN 1 END) as transactions
                FROM afi_plan_transacoes
                WHERE data_transacao >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY DATE(data_transacao)
                ORDER BY date
            ", [$interval]);

            return array_map(function ($item) {
                return [
                    'date' => $item->date,
                    'revenue' => (float) $item->revenue,
                    'transactions' => (int) $item->transactions
                ];
            }, $data);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Receita por gateway
     */
    private function getRevenueByGateway(string $period): array
    {
        $interval = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '12m' => 365,
            default => 30
        };

        try {
            $data = DB::select("
                SELECT 
                    g.nome as gateway_name,
                    g.provedor as provider,
                    COALESCE(SUM(CASE WHEN t.status = 'aprovada' THEN t.valor_final END), 0) as revenue,
                    COUNT(CASE WHEN t.status = 'aprovada' THEN 1 END) as transactions,
                    ROUND(
                        COUNT(CASE WHEN t.status = 'aprovada' THEN 1 END) * 100.0 / 
                        NULLIF(COUNT(t.id), 0), 2
                    ) as success_rate
                FROM afi_plan_gateways g
                LEFT JOIN afi_plan_transacoes t ON g.id = t.gateway_id
                    AND t.data_transacao >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY g.id, g.nome, g.provedor
                ORDER BY revenue DESC
            ", [$interval]);

            return array_map(function ($item) {
                return [
                    'gateway_name' => $item->gateway_name,
                    'provider' => $item->provider,
                    'revenue' => (float) $item->revenue,
                    'transactions' => (int) $item->transactions,
                    'success_rate' => (float) $item->success_rate
                ];
            }, $data);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Exportar receita
     */
    private function exportRevenue(Request $request)
    {
        $period = $request->get('period', '30d');
        $data = $this->getRevenueData($period);

        $csv = "Data,Receita,Transações\n";
        foreach ($data as $row) {
            $csv .= "{$row['date']},{$row['revenue']},{$row['transactions']}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="receita_' . $period . '.csv"'
        ]);
    }

    /**
     * Exportar transações
     */
    private function exportTransactions(Request $request)
    {
        try {
            $transactions = DB::select("
                SELECT 
                    t.id,
                    t.codigo_transacao,
                    t.valor_original,
                    t.valor_final,
                    t.status,
                    t.metodo_pagamento,
                    t.data_transacao,
                    e.nome_fantasia as empresa,
                    g.nome as gateway
                FROM afi_plan_transacoes t
                LEFT JOIN empresas e ON t.empresa_id = e.id
                LEFT JOIN afi_plan_gateways g ON t.gateway_id = g.id
                WHERE t.data_transacao >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY t.data_transacao DESC
                LIMIT 1000
            ");

            $csv = "ID,Código,Valor Original,Valor Final,Status,Método,Data,Empresa,Gateway\n";
            foreach ($transactions as $transaction) {
                $csv .= "{$transaction->id},{$transaction->codigo_transacao},{$transaction->valor_original},{$transaction->valor_final},{$transaction->status},{$transaction->metodo_pagamento},{$transaction->data_transacao},{$transaction->empresa},{$transaction->gateway}\n";
            }

            return Response::make($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="transacoes_' . date('Y-m-d') . '.csv"'
            ]);
        } catch (\Exception $e) {
            abort(500, 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }
}
