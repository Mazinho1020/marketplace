<?php

namespace App\Http\Controllers\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Vendas\Venda;
use App\Models\Vendas\VendaCancelamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Controller para gestão de cancelamentos de vendas
 * 
 * Gerencia o processo completo de cancelamento com workflow
 * de aprovação e processamento de reembolsos
 */
class VendaCancelamentoController extends Controller
{
    /**
     * Lista cancelamentos com filtros
     */
    public function index(Request $request): View
    {
        $cancelamentos = VendaCancelamento::porEmpresa(auth()->user()->empresa_id ?? 1)
            ->with(['venda', 'usuario', 'aprovadoPor']);

        // Aplicar filtros
        if ($request->filled('tipo')) {
            $cancelamentos->porTipo($request->tipo);
        }

        if ($request->filled('motivo')) {
            $cancelamentos->porMotivo($request->motivo);
        }

        if ($request->filled('status_aprovacao')) {
            if ($request->status_aprovacao === 'pendente') {
                $cancelamentos->pendentesAprovacao();
            } else {
                $cancelamentos->aprovados();
            }
        }

        if ($request->filled('data_inicio')) {
            $cancelamentos->where('data_cancelamento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $cancelamentos->where('data_cancelamento', '<=', $request->data_fim);
        }

        $cancelamentos = $cancelamentos->orderBy('created_at', 'desc')
                                     ->paginate(20);

        return view('vendas.cancelamentos.index', compact('cancelamentos'));
    }

    /**
     * Cria novo cancelamento
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'venda_id' => 'required|exists:lancamentos,id',
            'tipo_cancelamento' => 'required|in:total,parcial',
            'motivo_categoria' => 'required|in:cliente_desistiu,produto_indisponivel,erro_preco,problema_pagamento,outros',
            'motivo_detalhado' => 'required|string|max:500',
            'valor_cancelado' => 'required|numeric|min:0',
            'valor_reembolso' => 'nullable|numeric|min:0',
        ]);

        try {
            $venda = Venda::vendas()
                ->empresa(auth()->user()->empresa_id ?? 1)
                ->findOrFail($request->venda_id);

            // Verificar se a venda pode ser cancelada
            if (!$venda->podeSerCancelada()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venda não pode ser cancelada no status atual.',
                ], 400);
            }

            // Validar valor do cancelamento
            if ($request->valor_cancelado > $venda->getValorLiquidoAttribute()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valor do cancelamento não pode ser maior que o valor da venda.',
                ], 400);
            }

            $cancelamento = VendaCancelamento::create([
                'empresa_id' => $venda->empresa_id,
                'lancamento_id' => $venda->id,
                'tipo_cancelamento' => $request->tipo_cancelamento,
                'motivo_categoria' => $request->motivo_categoria,
                'motivo_detalhado' => $request->motivo_detalhado,
                'valor_cancelado' => $request->valor_cancelado,
                'valor_reembolso' => $request->valor_reembolso ?? 0,
                'usuario_id' => auth()->id(),
            ]);

            // Se não requer aprovação, aprovar automaticamente
            if (!$cancelamento->requerAprovacao()) {
                $cancelamento->aprovar(auth()->id(), $request->valor_reembolso);
            }

            // Alterar status da venda para cancelado
            $venda->alterarStatus(
                Venda::STATUS_CANCELADO,
                'Cancelamento: ' . $request->motivo_detalhado,
                ['cancelamento_id' => $cancelamento->id]
            );

            return response()->json([
                'success' => true,
                'message' => 'Cancelamento criado com sucesso!',
                'cancelamento' => $cancelamento->load(['venda']),
                'requer_aprovacao' => $cancelamento->requerAprovacao(),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar cancelamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Aprova um cancelamento
     */
    public function aprovar(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'valor_reembolso' => 'required|numeric|min:0',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        try {
            $cancelamento = VendaCancelamento::porEmpresa(auth()->user()->empresa_id ?? 1)
                ->findOrFail($id);

            if ($cancelamento->isAprovado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este cancelamento já foi aprovado.',
                ], 400);
            }

            // Validar valor do reembolso
            if ($request->valor_reembolso > $cancelamento->valor_cancelado) {
                return response()->json([
                    'success' => false,
                    'message' => 'Valor do reembolso não pode ser maior que o valor cancelado.',
                ], 400);
            }

            $sucesso = $cancelamento->aprovar(
                auth()->id(),
                $request->valor_reembolso,
                $request->observacoes
            );

            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao aprovar cancelamento.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cancelamento aprovado com sucesso!',
                'cancelamento' => $cancelamento->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar cancelamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Processa reembolso de um cancelamento
     */
    public function processarReembolso(int $id): JsonResponse
    {
        try {
            $cancelamento = VendaCancelamento::porEmpresa(auth()->user()->empresa_id ?? 1)
                ->findOrFail($id);

            if (!$cancelamento->isAprovado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancelamento deve ser aprovado antes do reembolso.',
                ], 400);
            }

            if ($cancelamento->isReembolsoProcessado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reembolso já foi processado.',
                ], 400);
            }

            if (!$cancelamento->temReembolso()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este cancelamento não possui valor de reembolso.',
                ], 400);
            }

            $sucesso = $cancelamento->processarReembolso();

            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar reembolso.',
                ], 500);
            }

            // Aqui você pode integrar com gateway de pagamento para processar o reembolso real
            // Por exemplo: processar estorno no cartão, PIX reverso, etc.

            return response()->json([
                'success' => true,
                'message' => 'Reembolso processado com sucesso!',
                'cancelamento' => $cancelamento->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar reembolso: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Estatísticas de cancelamentos
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

        $cancelamentos = VendaCancelamento::porEmpresa($empresaId)
            ->entreDatas($dataInicio, now()->endOfDay());

        $estatisticas = [
            'total_cancelamentos' => $cancelamentos->count(),
            'valor_total_cancelado' => $cancelamentos->sum('valor_cancelado'),
            'valor_total_reembolso' => $cancelamentos->sum('valor_reembolso'),
            'pendentes_aprovacao' => $cancelamentos->pendentesAprovacao()->count(),
            'reembolsos_pendentes' => $cancelamentos->aprovados()->where('data_reembolso', null)->count(),
            
            // Por tipo
            'por_tipo' => $cancelamentos->groupBy('tipo_cancelamento')
                                       ->selectRaw('tipo_cancelamento, COUNT(*) as total, SUM(valor_cancelado) as valor_total')
                                       ->pluck('total', 'tipo_cancelamento'),
            
            // Por motivo
            'por_motivo' => $cancelamentos->groupBy('motivo_categoria')
                                         ->selectRaw('motivo_categoria, COUNT(*) as total')
                                         ->pluck('total', 'motivo_categoria'),
            
            // Taxa de cancelamento (precisa calcular em relação às vendas)
            'taxa_cancelamento' => $this->calcularTaxaCancelamento($empresaId, $dataInicio),
        ];

        return response()->json([
            'success' => true,
            'data' => $estatisticas,
            'periodo' => $periodo,
        ]);
    }

    /**
     * Calcula taxa de cancelamento em relação às vendas
     */
    private function calcularTaxaCancelamento(int $empresaId, $dataInicio): float
    {
        $totalVendas = Venda::vendas()
            ->empresa($empresaId)
            ->where('created_at', '>=', $dataInicio)
            ->count();

        $totalCancelamentos = VendaCancelamento::porEmpresa($empresaId)
            ->entreDatas($dataInicio, now())
            ->count();

        if ($totalVendas === 0) {
            return 0.0;
        }

        return round(($totalCancelamentos / $totalVendas) * 100, 2);
    }

    /**
     * API: Motivos de cancelamento mais comuns
     */
    public function motivosComuns(Request $request): JsonResponse
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

        $motivos = VendaCancelamento::porEmpresa($empresaId)
            ->entreDatas($dataInicio, now())
            ->selectRaw('motivo_categoria, COUNT(*) as total, SUM(valor_cancelado) as valor_total')
            ->groupBy('motivo_categoria')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $motivos->map(function ($motivo) {
                return [
                    'categoria' => $motivo->motivo_categoria,
                    'categoria_formatada' => $motivo->motivo_formatado ?? $motivo->motivo_categoria,
                    'total' => $motivo->total,
                    'valor_total' => $motivo->valor_total,
                    'valor_formatado' => 'R$ ' . number_format($motivo->valor_total, 2, ',', '.'),
                ];
            }),
        ]);
    }
}