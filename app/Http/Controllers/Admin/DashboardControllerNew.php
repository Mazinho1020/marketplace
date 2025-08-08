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
        // Estatísticas gerais
        $stats = $this->getGeneralStats();

        // Gráfico de receita dos últimos 12 meses
        $revenueChart = $this->getRevenueChart();

        // Transações recentes
        $recentTransactions = $this->getRecentTransactions();

        // Estatísticas de pagamentos
        $paymentStats = $this->getPaymentStats();

        return view('admin.dashboard.index', compact(
            'stats',
            'revenueChart',
            'recentTransactions',
            'paymentStats'
        ));
    }

    /**
     * Obter estatísticas gerais do sistema
     */
    private function getGeneralStats(): array
    {
        try {
            // Estatísticas básicas do sistema
            $totalEmpresas = DB::table('empresas')->where('status', 'ativo')->count();
            $totalUsuarios = DB::table('pessoas')->count();
            $totalTransacoes = DB::table('afi_plan_transacoes')->count();
            $totalGateways = DB::table('afi_plan_gateways')->where('ativo', true)->count();

            return [
                'total_empresas' => $totalEmpresas,
                'total_usuarios' => $totalUsuarios,
                'total_transacoes' => $totalTransacoes,
                'total_gateways' => $totalGateways,
                'crescimento_empresas' => 0, // Placeholder
                'receita_mensal' => 0, // Placeholder
            ];
        } catch (\Exception $e) {
            return [
                'total_empresas' => 0,
                'total_usuarios' => 0,
                'total_transacoes' => 0,
                'total_gateways' => 0,
                'crescimento_empresas' => 0,
                'receita_mensal' => 0,
            ];
        }
    }

    /**
     * Gráfico de receita dos últimos 12 meses
     */
    private function getRevenueChart(): array
    {
        try {
            $data = DB::select("
                SELECT 
                    DATE_FORMAT(data_transacao, '%Y-%m') as month,
                    COALESCE(SUM(valor_final), 0) as revenue
                FROM afi_plan_transacoes 
                WHERE data_transacao >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    AND status = 'aprovada'
                GROUP BY DATE_FORMAT(data_transacao, '%Y-%m')
                ORDER BY month
            ");

            return array_map(function ($item) {
                return [
                    'month' => $item->month,
                    'revenue' => (float) $item->revenue
                ];
            }, $data);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Transações recentes
     */
    private function getRecentTransactions(): array
    {
        try {
            $transactions = DB::table('afi_plan_transacoes as t')
                ->select([
                    't.*',
                    'e.nome_fantasia as empresa_nome',
                    'g.nome as gateway_nome'
                ])
                ->leftJoin('empresas as e', 't.empresa_id', '=', 'e.id')
                ->leftJoin('afi_plan_gateways as g', 't.gateway_id', '=', 'g.id')
                ->orderBy('t.data_transacao', 'desc')
                ->limit(10)
                ->get();

            return array_map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'valor' => $transaction->valor_final ?? 0,
                    'status' => $transaction->status ?? 'pendente',
                    'empresa' => $transaction->empresa_nome ?? 'N/A',
                    'gateway' => $transaction->gateway_nome ?? 'N/A',
                    'data' => $transaction->data_transacao ?? now(),
                    'metodo' => $transaction->metodo_pagamento ?? 'N/A'
                ];
            }, $transactions->toArray());
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Estatísticas de pagamentos
     */
    private function getPaymentStats(): array
    {
        try {
            $stats = DB::selectOne("
                SELECT 
                    COUNT(*) as total_transacoes,
                    COUNT(CASE WHEN status = 'aprovada' THEN 1 END) as aprovadas,
                    COUNT(CASE WHEN status = 'pendente' THEN 1 END) as pendentes,
                    COUNT(CASE WHEN status = 'cancelada' THEN 1 END) as canceladas,
                    COALESCE(SUM(CASE WHEN status = 'aprovada' THEN valor_final END), 0) as volume_aprovado,
                    COALESCE(AVG(CASE WHEN status = 'aprovada' THEN valor_final END), 0) as ticket_medio
                FROM afi_plan_transacoes
                WHERE data_transacao >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");

            return [
                'total_transacoes' => (int) $stats->total_transacoes,
                'aprovadas' => (int) $stats->aprovadas,
                'pendentes' => (int) $stats->pendentes,
                'canceladas' => (int) $stats->canceladas,
                'volume_aprovado' => (float) $stats->volume_aprovado,
                'ticket_medio' => (float) $stats->ticket_medio,
                'taxa_aprovacao' => $stats->total_transacoes > 0 ?
                    round(($stats->aprovadas / $stats->total_transacoes) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            return [
                'total_transacoes' => 0,
                'aprovadas' => 0,
                'pendentes' => 0,
                'canceladas' => 0,
                'volume_aprovado' => 0,
                'ticket_medio' => 0,
                'taxa_aprovacao' => 0
            ];
        }
    }
}
