<?php

namespace App\Http\Controllers\Api\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Financeiro\Lancamento;
use App\Models\Financeiro\LancamentoMovimentacao;
use App\Services\Financeiro\LancamentoService;
use App\Http\Requests\Financeiro\LancamentoRequest;
use App\Http\Resources\Financeiro\LancamentoResource;
use App\Http\Resources\Financeiro\LancamentoMovimentacaoResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Controller para API de Lançamentos Financeiros
 * 
 * Endpoints completos para gestão de lançamentos:
 * - CRUD de lançamentos
 * - Gestão de pagamentos/recebimentos
 * - Relatórios financeiros
 * - Workflow de aprovação
 */
class LancamentoController extends Controller
{
    protected LancamentoService $lancamentoService;

    public function __construct(LancamentoService $lancamentoService)
    {
        $this->lancamentoService = $lancamentoService;
    }

    /**
     * Listar lançamentos com filtros
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->input('empresa_id', auth()->user()->empresa_id ?? 1);
            
            $query = Lancamento::empresa($empresaId)->ativo();
            
            // Aplicar filtros
            if ($request->filled('natureza')) {
                $query->porNatureza($request->input('natureza'));
            }
            
            if ($request->filled('situacao')) {
                $query->where('situacao_financeira', $request->input('situacao'));
            }
            
            if ($request->filled('categoria')) {
                $query->where('categoria', $request->input('categoria'));
            }
            
            if ($request->filled('data_inicio') && $request->filled('data_fim')) {
                $query->vencimentoEntre($request->input('data_inicio'), $request->input('data_fim'));
            }
            
            if ($request->filled('pessoa_id')) {
                $query->where('pessoa_id', $request->input('pessoa_id'));
            }
            
            if ($request->filled('numero_documento')) {
                $query->where('numero_documento', 'like', '%' . $request->input('numero_documento') . '%');
            }
            
            if ($request->filled('descricao')) {
                $query->where('descricao', 'like', '%' . $request->input('descricao') . '%');
            }
            
            // Ordenação
            $orderBy = $request->input('order_by', 'data_vencimento');
            $orderDirection = $request->input('order_direction', 'asc');
            $query->orderBy($orderBy, $orderDirection);
            
            // Paginação
            $perPage = $request->input('per_page', 15);
            $lancamentos = $query->with(['itens', 'movimentacoes'])->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => LancamentoResource::collection($lancamentos),
                'meta' => [
                    'current_page' => $lancamentos->currentPage(),
                    'last_page' => $lancamentos->lastPage(),
                    'per_page' => $lancamentos->perPage(),
                    'total' => $lancamentos->total(),
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar lançamentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter um lançamento específico
     */
    public function show(string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::with(['itens', 'movimentacoes'])
                                   ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => new LancamentoResource($lancamento)
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lançamento não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Criar um novo lançamento
     */
    public function store(LancamentoRequest $request): JsonResponse
    {
        try {
            $dados = $request->validated();
            $dados['usuario_id'] = auth()->id() ?? 1;
            $dados['empresa_id'] = $request->input('empresa_id', auth()->user()->empresa_id ?? 1);
            
            $lancamento = $this->lancamentoService->criarLancamento($dados);
            
            return response()->json([
                'success' => true,
                'message' => 'Lançamento criado com sucesso',
                'data' => new LancamentoResource($lancamento)
            ], 201);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar lançamento',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Atualizar um lançamento
     */
    public function update(LancamentoRequest $request, string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            $dados = $request->validated();
            
            $lancamentoAtualizado = $this->lancamentoService->atualizarLancamento($lancamento, $dados);
            
            return response()->json([
                'success' => true,
                'message' => 'Lançamento atualizado com sucesso',
                'data' => new LancamentoResource($lancamentoAtualizado)
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar lançamento',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Excluir um lançamento (soft delete)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            
            // Verificar se pode ser excluído
            if ($lancamento->valor_pago > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir lançamento com pagamentos registrados'
                ], 422);
            }
            
            $lancamento->update([
                'data_exclusao' => now(),
                'usuario_exclusao' => auth()->id(),
                'motivo_exclusao' => 'Exclusão via API'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Lançamento excluído com sucesso'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir lançamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar pagamento/recebimento
     */
    public function registrarPagamento(Request $request, string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            
            $request->validate([
                'valor' => 'required|numeric|min:0.01',
                'data_movimentacao' => 'nullable|date',
                'forma_pagamento_id' => 'nullable|integer',
                'conta_bancaria_id' => 'nullable|integer',
                'numero_documento' => 'nullable|string|max:100',
                'observacoes' => 'nullable|string',
            ]);
            
            $dados = $request->all();
            $dados['data_movimentacao'] = $dados['data_movimentacao'] ?? now();
            $dados['usuario_id'] = auth()->id() ?? 1;
            
            $movimentacao = $this->lancamentoService->registrarPagamento($lancamento, $dados);
            
            return response()->json([
                'success' => true,
                'message' => 'Pagamento registrado com sucesso',
                'data' => [
                    'movimentacao' => new LancamentoMovimentacaoResource($movimentacao),
                    'lancamento' => new LancamentoResource($lancamento->fresh(['itens', 'movimentacoes']))
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar pagamento',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Estornar pagamento
     */
    public function estornarPagamento(Request $request, string $movimentacaoId): JsonResponse
    {
        try {
            $movimentacao = LancamentoMovimentacao::findOrFail($movimentacaoId);
            
            $request->validate([
                'motivo' => 'required|string|max:500'
            ]);
            
            $estorno = $this->lancamentoService->estornarPagamento($movimentacao, $request->input('motivo'));
            
            return response()->json([
                'success' => true,
                'message' => 'Pagamento estornado com sucesso',
                'data' => [
                    'estorno' => new LancamentoMovimentacaoResource($estorno),
                    'lancamento' => new LancamentoResource($movimentacao->lancamento->fresh(['itens', 'movimentacoes']))
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar pagamento',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Criar parcelas de um lançamento
     */
    public function criarParcelas(Request $request, string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            
            $request->validate([
                'total_parcelas' => 'required|integer|min:2|max:60',
                'intervalo_dias' => 'nullable|integer|min:1|max:365',
            ]);
            
            if ($lancamento->isParcelado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lançamento já está parcelado'
                ], 422);
            }
            
            $dados = array_merge($lancamento->toArray(), $request->all());
            $parcelas = $this->lancamentoService->criarParcelas($lancamento, $dados);
            
            return response()->json([
                'success' => true,
                'message' => 'Parcelas criadas com sucesso',
                'data' => LancamentoResource::collection($parcelas)
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar parcelas',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Aprovar lançamento
     */
    public function aprovar(Request $request, string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            
            $request->validate([
                'observacoes' => 'nullable|string|max:500'
            ]);
            
            $sucesso = $lancamento->aprovar(
                auth()->id() ?? 1,
                $request->input('observacoes')
            );
            
            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lançamento não pode ser aprovado no estado atual'
                ], 422);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Lançamento aprovado com sucesso',
                'data' => new LancamentoResource($lancamento->fresh())
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aprovar lançamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeitar lançamento
     */
    public function rejeitar(Request $request, string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            
            $request->validate([
                'motivo' => 'required|string|max:500'
            ]);
            
            $sucesso = $lancamento->rejeitar(
                auth()->id() ?? 1,
                $request->input('motivo')
            );
            
            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lançamento não pode ser rejeitado no estado atual'
                ], 422);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Lançamento rejeitado com sucesso',
                'data' => new LancamentoResource($lancamento->fresh())
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao rejeitar lançamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar lançamento
     */
    public function cancelar(Request $request, string $id): JsonResponse
    {
        try {
            $lancamento = Lancamento::findOrFail($id);
            
            $request->validate([
                'motivo' => 'required|string|max:500'
            ]);
            
            $sucesso = $lancamento->cancelar(
                auth()->id() ?? 1,
                $request->input('motivo')
            );
            
            if (!$sucesso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cancelar lançamento'
                ], 422);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Lançamento cancelado com sucesso',
                'data' => new LancamentoResource($lancamento->fresh())
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar lançamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter relatório financeiro
     */
    public function relatorioFinanceiro(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->input('empresa_id', auth()->user()->empresa_id ?? 1);
            
            $filtros = $request->only([
                'data_inicio',
                'data_fim',
                'natureza',
                'situacao'
            ]);
            
            $relatorio = $this->lancamentoService->obterRelatorioFinanceiro($empresaId, $filtros);
            
            return response()->json([
                'success' => true,
                'data' => $relatorio
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter lançamentos vencidos
     */
    public function vencidos(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->input('empresa_id', auth()->user()->empresa_id ?? 1);
            $dias = $request->input('dias', 0); // dias em atraso
            
            $query = Lancamento::empresa($empresaId)
                              ->ativo()
                              ->where('situacao_financeira', Lancamento::SITUACAO_VENCIDO);
            
            if ($dias > 0) {
                $dataLimite = now()->subDays($dias)->format('Y-m-d');
                $query->where('data_vencimento', '<=', $dataLimite);
            }
            
            $lancamentos = $query->with(['itens', 'movimentacoes'])
                                ->orderBy('data_vencimento', 'asc')
                                ->get();
            
            return response()->json([
                'success' => true,
                'data' => LancamentoResource::collection($lancamentos),
                'meta' => [
                    'total' => $lancamentos->count(),
                    'valor_total' => $lancamentos->sum('valor_saldo')
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar lançamentos vencidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard financeiro
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->input('empresa_id', auth()->user()->empresa_id ?? 1);
            
            $hoje = now()->format('Y-m-d');
            $proximoMes = now()->addMonth()->format('Y-m-d');
            
            $base = Lancamento::empresa($empresaId)->ativo();
            
            $dashboard = [
                'totais' => [
                    'a_receber' => (clone $base)->contasReceber()->sum('valor_saldo'),
                    'a_pagar' => (clone $base)->contasPagar()->sum('valor_saldo'),
                    'vencidos' => (clone $base)->vencidos()->sum('valor_saldo'),
                    'vencendo_30_dias' => (clone $base)->pendentes()
                                                      ->vencimentoEntre($hoje, $proximoMes)
                                                      ->sum('valor_saldo'),
                ],
                'por_situacao' => [
                    'pendente' => (clone $base)->pendentes()->count(),
                    'pago' => (clone $base)->pagos()->count(),
                    'vencido' => (clone $base)->vencidos()->count(),
                    'parcialmente_pago' => (clone $base)->where('situacao_financeira', Lancamento::SITUACAO_PARCIALMENTE_PAGO)->count(),
                ],
                'movimentacao_mensal' => $this->obterMovimentacaoMensal($empresaId),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $dashboard
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método auxiliar para movimentação mensal
     */
    private function obterMovimentacaoMensal(int $empresaId): array
    {
        $inicioMes = now()->startOfMonth()->format('Y-m-d');
        $fimMes = now()->endOfMonth()->format('Y-m-d');
        
        $movimentacoes = LancamentoMovimentacao::where('empresa_id', $empresaId)
                                              ->whereBetween('data_movimentacao', [$inicioMes, $fimMes])
                                              ->selectRaw('
                                                  DATE(data_movimentacao) as data,
                                                  tipo,
                                                  SUM(valor) as total
                                              ')
                                              ->groupBy('data', 'tipo')
                                              ->get();
        
        return $movimentacoes->groupBy('data')->map(function ($grupo) {
            return [
                'recebimentos' => $grupo->where('tipo', 'recebimento')->sum('total'),
                'pagamentos' => $grupo->where('tipo', 'pagamento')->sum('total'),
                'estornos' => $grupo->where('tipo', 'estorno')->sum('total'),
            ];
        })->toArray();
    }
}
