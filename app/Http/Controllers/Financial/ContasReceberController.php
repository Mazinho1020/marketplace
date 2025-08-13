<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\ContaReceberRequest;
use App\Http\Requests\Financial\PagamentoRequest;
use App\Services\Financial\ContasReceberService;
use App\DTOs\Financial\ContaReceberDTO;
use App\Models\Financial\LancamentoFinanceiro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContasReceberController extends Controller
{
    public function __construct(
        private ContasReceberService $contasReceberService
    ) {}

    /**
     * GET /api/contas-receber - Listar contas a receber com filtros
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

            $contas = $this->contasReceberService->buscar($empresaId, $filtros);

            return response()->json([
                'success' => true,
                'data' => $contas,
                'total' => $contas->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas a receber: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-receber - Criar nova conta a receber
     */
    public function store(ContaReceberRequest $request): JsonResponse
    {
        try {
            $dados = ContaReceberDTO::fromRequest($request->validated());
            
            if ($request->input('parcelado', false)) {
                $parcelas = $request->input('parcelas', 1);
                $contas = $this->contasReceberService->criarParcelado($dados, $parcelas);
                
                return response()->json([
                    'success' => true,
                    'message' => "Criadas {$parcelas} parcelas com sucesso",
                    'data' => $contas
                ], 201);
            } else {
                $conta = $this->contasReceberService->criar($dados);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Conta a receber criada com sucesso',
                    'data' => $conta
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar conta a receber: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-receber/{id} - Detalhar conta a receber
     */
    public function show(int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::with([
                'pessoa', 'contaGerencial', 'pagamentos', 'usuario'
            ])->findOrFail($id);

            if (!$conta->isContaReceber()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a receber'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $conta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar conta a receber: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/contas-receber/{id} - Atualizar conta a receber
     */
    public function update(ContaReceberRequest $request, int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if (!$conta->isContaReceber()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a receber'
                ], 400);
            }

            if ($conta->isPaga()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível editar uma conta já recebida'
                ], 400);
            }

            $conta->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Conta a receber atualizada com sucesso',
                'data' => $conta->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar conta a receber: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-receber/{id}/receber - Efetuar recebimento
     */
    public function receber(PagamentoRequest $request, int $id): JsonResponse
    {
        try {
            $valor = $request->input('valor');
            $dadosRecebimento = $request->except(['valor']);

            $sucesso = $this->contasReceberService->receber($id, $valor, $dadosRecebimento);

            if ($sucesso) {
                $conta = LancamentoFinanceiro::with('pagamentos')->findOrFail($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Recebimento registrado com sucesso',
                    'data' => $conta
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar recebimento'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/contas-receber/{id}/pagamentos - Listar recebimentos
     */
    public function pagamentos(int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if (!$conta->isContaReceber()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a receber'
                ], 400);
            }

            $recebimentos = $conta->pagamentos()->with(['formaPagamento', 'usuario'])->get();

            return response()->json([
                'success' => true,
                'data' => $recebimentos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar recebimentos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/contas-receber/pagamentos/{id} - Estornar recebimento
     */
    public function estornarPagamento(int $pagamentoId): JsonResponse
    {
        try {
            $pagamento = \App\Models\Financial\Pagamento::findOrFail($pagamentoId);
            
            if (!$pagamento->lancamento->isContaReceber()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este recebimento não pertence a uma conta a receber'
                ], 400);
            }

            $pagamento->cancelar('Estornado pelo usuário');

            return response()->json([
                'success' => true,
                'message' => 'Recebimento estornado com sucesso',
                'data' => $pagamento->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar recebimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-receber/{id}/cancelar - Cancelar conta
     */
    public function cancelar(Request $request, int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if (!$conta->isContaReceber()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este lançamento não é uma conta a receber'
                ], 400);
            }

            if ($conta->isPaga()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível cancelar uma conta já recebida'
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
     * GET /api/contas-receber/dashboard - Dashboard resumo
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

            $dashboard = $this->contasReceberService->getDashboard($empresaId);

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
     * GET /api/contas-receber/inadimplencia - Relatório de inadimplência
     */
    public function inadimplencia(Request $request): JsonResponse
    {
        try {
            $empresaId = $request->get('empresa_id');
            
            if (!$empresaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID da empresa é obrigatório'
                ], 400);
            }

            $relatorio = $this->contasReceberService->getRelatorioInadimplencia($empresaId);

            return response()->json([
                'success' => true,
                'data' => $relatorio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório de inadimplência: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-receber/vencidas - Contas vencidas
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

            $contasVencidas = $this->contasReceberService->getVencidas($empresaId);

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