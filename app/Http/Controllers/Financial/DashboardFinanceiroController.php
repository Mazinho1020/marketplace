<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Services\Financial\ContasPagarService;
use App\Services\Financial\ContasReceberService;
use App\Services\Financial\CobrancaAutomaticaService;
use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardFinanceiroController extends Controller
{
    public function __construct(
        private ContasPagarService $contasPagarService,
        private ContasReceberService $contasReceberService,
        private CobrancaAutomaticaService $cobrancaService
    ) {}

    /**
     * GET /api/financial/dashboard - Dashboard completo
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $dashboard = [
                'resumo_geral' => $this->getResumoGeral($empresaId),
                'contas_pagar' => $this->contasPagarService->getDashboard($empresaId),
                'contas_receber' => $this->contasReceberService->getDashboard($empresaId),
                'fluxo_caixa' => $this->getFluxoCaixa($empresaId),
                'cobracas' => $this->cobrancaService->getResumoCobrancas($empresaId),
                'graficos' => $this->getDadosGraficos($empresaId),
                'ultimas_atualizacao' => now(),
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboard
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/financial/dashboard/resumo - Resumo executivo
     */
    public function resumo(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $resumo = $this->getResumoGeral($empresaId);

            return response()->json([
                'success' => true,
                'data' => $resumo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar resumo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/financial/dashboard/fluxo-caixa - Fluxo de caixa projetado
     */
    public function fluxoCaixa(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            $dias = $request->get('dias', 30);
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $fluxoCaixa = $this->getFluxoCaixa($empresaId, $dias);

            return response()->json([
                'success' => true,
                'data' => $fluxoCaixa
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar fluxo de caixa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/financial/dashboard/graficos - Dados para gráficos
     */
    public function graficos(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            $periodo = $request->get('periodo', 12); // meses
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $graficos = $this->getDadosGraficos($empresaId, $periodo);

            return response()->json([
                'success' => true,
                'data' => $graficos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar dados de gráficos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter resumo geral da situação financeira
     */
    private function getResumoGeral(int $empresaId): array
    {
        $baseQuery = LancamentoFinanceiro::where('empresa_id', $empresaId);
        
        $contasPagar = $baseQuery->clone()->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR);
        $contasReceber = $baseQuery->clone()->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER);

        return [
            'saldo_pendente' => [
                'receber' => $contasReceber->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)->sum('valor_final'),
                'pagar' => $contasPagar->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)->sum('valor_final'),
            ],
            'movimentacao_mes' => [
                'recebido' => $contasReceber->clone()
                    ->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
                    ->whereMonth('data_pagamento', now()->month)
                    ->whereYear('data_pagamento', now()->year)
                    ->sum('valor_final'),
                'pago' => $contasPagar->clone()
                    ->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
                    ->whereMonth('data_pagamento', now()->month)
                    ->whereYear('data_pagamento', now()->year)
                    ->sum('valor_final'),
            ],
            'vencidas' => [
                'receber_quantidade' => $contasReceber->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)->count(),
                'receber_valor' => $contasReceber->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)->sum('valor_final'),
                'pagar_quantidade' => $contasPagar->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)->count(),
                'pagar_valor' => $contasPagar->clone()->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)->sum('valor_final'),
            ],
            'resumo_situacao' => [
                'liquidez_corrente' => 0, // Calculado baseado nas contas pendentes
                'inadimplencia_percentual' => 0, // Calculado baseado nas contas vencidas
                'ticket_medio_receber' => 0,
                'ticket_medio_pagar' => 0,
            ]
        ];
    }

    /**
     * Obter fluxo de caixa projetado
     */
    private function getFluxoCaixa(int $empresaId, int $dias = 30): array
    {
        $dataInicio = now();
        $dataFim = now()->addDays($dias);
        
        $fluxo = [];
        $saldoAcumulado = 0;

        // Gerar dados diários
        for ($data = $dataInicio->copy(); $data <= $dataFim; $data->addDay()) {
            $dataFormatada = $data->format('Y-m-d');
            
            // Recebimentos previstos
            $recebimentosPrevisto = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                ->whereDate('data_vencimento', $dataFormatada)
                ->sum('valor_final');

            // Pagamentos previstos
            $pagamentosPrevisto = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                ->whereDate('data_vencimento', $dataFormatada)
                ->sum('valor_final');

            $saldoDia = $recebimentosPrevisto - $pagamentosPrevisto;
            $saldoAcumulado += $saldoDia;

            $fluxo[] = [
                'data' => $dataFormatada,
                'data_formatada' => $data->format('d/m/Y'),
                'recebimentos' => $recebimentosPrevisto,
                'pagamentos' => $pagamentosPrevisto,
                'saldo_dia' => $saldoDia,
                'saldo_acumulado' => $saldoAcumulado,
            ];
        }

        return [
            'periodo' => [
                'inicio' => $dataInicio->format('Y-m-d'),
                'fim' => $dataFim->format('Y-m-d'),
                'dias' => $dias
            ],
            'resumo' => [
                'total_recebimentos' => collect($fluxo)->sum('recebimentos'),
                'total_pagamentos' => collect($fluxo)->sum('pagamentos'),
                'saldo_final' => $saldoAcumulado,
            ],
            'detalhes' => $fluxo
        ];
    }

    /**
     * Obter dados para gráficos
     */
    private function getDadosGraficos(int $empresaId, int $meses = 12): array
    {
        $dataInicio = now()->subMonths($meses);
        
        // Evolução mensal
        $evolucaoMensal = [];
        for ($i = $meses; $i >= 0; $i--) {
            $data = now()->subMonths($i);
            $mes = $data->format('Y-m');
            
            $recebido = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
                ->whereYear('data_pagamento', $data->year)
                ->whereMonth('data_pagamento', $data->month)
                ->sum('valor_final');

            $pago = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::PAGAR)
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PAGO)
                ->whereYear('data_pagamento', $data->year)
                ->whereMonth('data_pagamento', $data->month)
                ->sum('valor_final');

            $evolucaoMensal[] = [
                'mes' => $mes,
                'mes_formatado' => $data->format('M/Y'),
                'recebido' => $recebido,
                'pago' => $pago,
                'saldo' => $recebido - $pago,
            ];
        }

        // Situação atual por categoria
        $situacaoAtual = [
            'pendente' => LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('situacao_financeira', SituacaoFinanceiraEnum::PENDENTE)
                ->selectRaw('natureza_financeira, sum(valor_final) as total')
                ->groupBy('natureza_financeira')
                ->get()
                ->pluck('total', 'natureza_financeira'),
            
            'vencido' => LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('situacao_financeira', SituacaoFinanceiraEnum::VENCIDO)
                ->selectRaw('natureza_financeira, sum(valor_final) as total')
                ->groupBy('natureza_financeira')
                ->get()
                ->pluck('total', 'natureza_financeira'),
        ];

        return [
            'evolucao_mensal' => $evolucaoMensal,
            'situacao_atual' => $situacaoAtual,
            'periodo_analise' => [
                'inicio' => $dataInicio->format('Y-m-d'),
                'fim' => now()->format('Y-m-d'),
                'meses' => $meses
            ]
        ];
    }
}