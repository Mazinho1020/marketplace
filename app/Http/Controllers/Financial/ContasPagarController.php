<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\ContaPagarRequest;
use App\Http\Requests\Financial\PagamentoRequest;
use App\Services\Financial\ContasPagarService;
use App\DTOs\Financial\ContaPagarDTO;
use App\Models\Financial\LancamentoFinanceiro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContasPagarController extends Controller
{
    public function __construct(
        private ContasPagarService $contasPagarService
    ) {}

    /**
     * GET /api/contas-pagar - Listar contas a pagar com filtros
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

            $filtros = $request->only([
                'situacao', 'pessoa_id', 'data_inicio', 'data_fim', 
                'valor_min', 'valor_max', 'page', 'per_page'
            ]);

            $contas = $this->contasPagarService->buscar($empresaId, $filtros);

            return response()->json([
                'success' => true,
                'data' => $contas,
                'total' => $contas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas a pagar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-pagar - Criar nova conta a pagar
     */
    public function store(ContaPagarRequest $request): JsonResponse
    {
        try {
            $dados = ContaPagarDTO::fromRequest($request->validated());
            
            if ($request->input('parcelado', false)) {
                $parcelas = $request->input('parcelas', 1);
                $contas = $this->contasPagarService->criarParcelado($dados, $parcelas);
                
                return response()->json([
                    'success' => true,
                    'message' => "Criadas {$parcelas} parcelas com sucesso",
                    'data' => $contas
                ], 201);
            } else {
                $conta = $this->contasPagarService->criar($dados);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Conta a pagar criada com sucesso',
                    'data' => $conta
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar conta a pagar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-pagar/{id} - Detalhar conta a pagar
     */
    public function show(int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::with([
                'pessoa', 'contaGerencial', 'pagamentos', 'usuario'
            ])->findOrFail($id);

            if (!$conta->isContaPagar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $conta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar conta a pagar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/contas-pagar/{id} - Atualizar conta a pagar
     */
    public function update(ContaPagarRequest $request, int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if (!$conta->isContaPagar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            if ($conta->isPaga()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível editar uma conta já paga'
                ], 400);
            }

            $conta->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Conta a pagar atualizada com sucesso',
                'data' => $conta->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar conta a pagar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-pagar/{id}/pagar - Efetuar pagamento
     */
    public function pagar(PagamentoRequest $request, int $id): JsonResponse
    {
        try {
            $valor = $request->input('valor');
            $dadosPagamento = $request->except(['valor']);

            $sucesso = $this->contasPagarService->pagar($id, $valor, $dadosPagamento);

            if ($sucesso) {
                $conta = LancamentoFinanceiro::with('pagamentos')->findOrFail($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pagamento registrado com sucesso',
                    'data' => $conta
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar pagamento'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/contas-pagar/{id}/pagamentos - Listar pagamentos
     */
    public function pagamentos(int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if (!$conta->isContaPagar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            $pagamentos = $conta->pagamentos()->with(['formaPagamento', 'usuario'])->get();

            return response()->json([
                'success' => true,
                'data' => $pagamentos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pagamentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/contas-pagar/pagamentos/{id} - Estornar pagamento
     */
    public function estornarPagamento(int $pagamentoId): JsonResponse
    {
        try {
            $pagamento = \App\Models\Financial\Pagamento::findOrFail($pagamentoId);
            
            if (!$pagamento->lancamento->isContaPagar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pagamento não pertence a uma conta a pagar'
                ], 400);
            }

            $pagamento->cancelar('Estornado pelo usuário');

            return response()->json([
                'success' => true,
                'message' => 'Pagamento estornado com sucesso',
                'data' => $pagamento->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar pagamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-pagar/{id}/cancelar - Cancelar conta
     */
    public function cancelar(Request $request, int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if (!$conta->isContaPagar()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            if ($conta->isPaga()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível cancelar uma conta já paga'
                ], 400);
            }

            $conta->situacao_financeira = \App\Enums\SituacaoFinanceiraEnum::CANCELADO;
            $conta->save();

            return response()->json([
                'success' => true,
                'message' => 'Conta cancelada com sucesso',
                'data' => $conta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar conta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-pagar/dashboard - Dashboard resumo
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $dashboard = $this->contasPagarService->getDashboard($empresaId);

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
     * GET /api/contas-pagar/vencidas - Contas vencidas
     */
    public function vencidas(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $contasVencidas = $this->contasPagarService->getVencidas($empresaId);

            return response()->json([
                'success' => true,
                'data' => $contasVencidas,
                'total' => $contasVencidas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas vencidas: ' . $e->getMessage()
            ], 500);
        }
    }
}