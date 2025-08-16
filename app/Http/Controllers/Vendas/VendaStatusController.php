<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Vendas\Venda;
use App\Models\Vendas\VendaStatusHistorico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Controller para gestão de status de vendas
 * 
 * Gerencia o workflow de status das vendas com histórico completo
 * e validações de transição
 */
class VendaStatusController extends Controller
{
    /**
     * Retorna histórico de status de uma venda
     */
    public function historico(int $vendaId): JsonResponse
    {
        $venda = Venda::vendas()
            ->empresa(auth()->user()->empresa_id ?? 1)
            ->findOrFail($vendaId);

        $historico = VendaStatusHistorico::porVenda($vendaId)
            ->with(['usuario'])
            ->ordenadoPorData('asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $historico->map(function ($item) {
                return [
                    'id' => $item->id,
                    'status_anterior' => $item->status_anterior_formatado,
                    'status_novo' => $item->status_novo_formatado,
                    'motivo' => $item->motivo,
                    'observacoes' => $item->observacoes,
                    'usuario_nome' => $item->usuario->nome ?? 'Sistema',
                    'data_mudanca' => $item->data_mudanca->format('d/m/Y H:i:s'),
                    'dados_contexto' => $item->dados_contexto,
                ];
            }),
        ]);
    }

    /**
     * Altera status de uma venda com validações
     */
    public function alterar(Request $request): JsonResponse
    {
        $request->validate([
            'venda_id' => 'required|exists:lancamentos,id',
            'novo_status' => 'required|string',
            'motivo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string|max:1000',
            'dados_contexto' => 'nullable|array',
        ]);

        try {
            $venda = Venda::vendas()
                ->empresa(auth()->user()->empresa_id ?? 1)
                ->findOrFail($request->venda_id);

            // Verificar se a transição é válida
            if (!$venda->podeAlterarPara($request->novo_status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transição de status não permitida.',
                    'status_atual' => $venda->situacao_financeira,
                    'status_permitidos' => $this->getStatusPermitidos($venda->situacao_financeira),
                ], 400);
            }

            $sucesso = $venda->alterarStatus(
                $request->novo_status,
                $request->motivo,
                $request->dados_contexto ?? []
            );

            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao alterar status da venda.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status alterado com sucesso!',
                'venda' => [
                    'id' => $venda->id,
                    'numero_venda' => $venda->numero_venda,
                    'status_atual' => $venda->situacao_financeira,
                    'status_formatado' => $venda->status_formatado,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna o workflow completo de status de vendas
     */
    public function workflow(): JsonResponse
    {
        $workflow = [
            'rascunho' => [
                'nome' => 'Rascunho',
                'descricao' => 'Venda sendo criada',
                'cor' => '#6c757d',
                'icone' => 'fa-edit',
                'proximos' => ['pendente', 'cancelado'],
            ],
            'pendente' => [
                'nome' => 'Pendente',
                'descricao' => 'Aguardando confirmação/pagamento',
                'cor' => '#ffc107',
                'icone' => 'fa-clock',
                'proximos' => ['confirmado', 'cancelado'],
            ],
            'confirmado' => [
                'nome' => 'Confirmado',
                'descricao' => 'Pagamento aprovado',
                'cor' => '#28a745',
                'icone' => 'fa-check',
                'proximos' => ['processando', 'cancelado'],
            ],
            'processando' => [
                'nome' => 'Processando',
                'descricao' => 'Preparando pedido',
                'cor' => '#17a2b8',
                'icone' => 'fa-cogs',
                'proximos' => ['separando', 'cancelado'],
            ],
            'separando' => [
                'nome' => 'Separando',
                'descricao' => 'Separando produtos',
                'cor' => '#fd7e14',
                'icone' => 'fa-boxes',
                'proximos' => ['enviado', 'cancelado'],
            ],
            'enviado' => [
                'nome' => 'Enviado',
                'descricao' => 'Em transporte',
                'cor' => '#6f42c1',
                'icone' => 'fa-truck',
                'proximos' => ['entregue', 'devolvido'],
            ],
            'entregue' => [
                'nome' => 'Entregue',
                'descricao' => 'Finalizado com sucesso',
                'cor' => '#20c997',
                'icone' => 'fa-check-circle',
                'proximos' => ['devolvido'],
            ],
            'cancelado' => [
                'nome' => 'Cancelado',
                'descricao' => 'Cancelamento realizado',
                'cor' => '#dc3545',
                'icone' => 'fa-times-circle',
                'proximos' => [],
            ],
            'devolvido' => [
                'nome' => 'Devolvido',
                'descricao' => 'Produto retornado',
                'cor' => '#e83e8c',
                'icone' => 'fa-undo',
                'proximos' => [],
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $workflow,
        ]);
    }

    /**
     * Retorna os status permitidos para uma transição
     */
    private function getStatusPermitidos(string $statusAtual): array
    {
        $transicoesPermitidas = [
            'rascunho' => ['pendente', 'cancelado'],
            'pendente' => ['confirmado', 'cancelado'],
            'confirmado' => ['processando', 'cancelado'],
            'processando' => ['separando', 'cancelado'],
            'separando' => ['enviado', 'cancelado'],
            'enviado' => ['entregue', 'devolvido'],
            'entregue' => ['devolvido'],
            'cancelado' => [],
            'devolvido' => [],
        ];

        return $transicoesPermitidas[$statusAtual] ?? [];
    }

    /**
     * API: Estatísticas de status
     */
    public function estatisticas(Request $request): JsonResponse
    {
        $empresaId = auth()->user()->empresa_id ?? 1;
        $periodo = $request->periodo ?? 'mes';

        $dataInicio = match($periodo) {
            'hoje' => now()->startOfDay(),
            'semana' => now()->startOfWeek(),
            'mes' => now()->startOfMonth(),
            'ano' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        // Estatísticas por status
        $estatisticas = Venda::vendas()
            ->empresa($empresaId)
            ->where('created_at', '>=', $dataInicio)
            ->selectRaw('situacao_financeira, COUNT(*) as total, SUM(valor_bruto) as valor_total')
            ->groupBy('situacao_financeira')
            ->get()
            ->keyBy('situacao_financeira');

        // Transições mais comuns no período
        $transicoesComuns = VendaStatusHistorico::porEmpresa($empresaId)
            ->entreDatas($dataInicio, now())
            ->selectRaw('status_anterior, status_novo, COUNT(*) as total')
            ->groupBy(['status_anterior', 'status_novo'])
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Tempo médio por status
        $tempoMedio = VendaStatusHistorico::porEmpresa($empresaId)
            ->selectRaw('
                status_novo, 
                AVG(TIMESTAMPDIFF(HOUR, LAG(data_mudanca) OVER (PARTITION BY lancamento_id ORDER BY data_mudanca), data_mudanca)) as tempo_medio_horas
            ')
            ->groupBy('status_novo')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'estatisticas_por_status' => $estatisticas,
                'transicoes_comuns' => $transicoesComuns,
                'tempo_medio_por_status' => $tempoMedio,
                'periodo' => $periodo,
            ],
        ]);
    }
}