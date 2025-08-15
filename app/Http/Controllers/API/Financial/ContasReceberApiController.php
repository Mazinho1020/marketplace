<?php

namespace App\Http\Controllers\Api\Financial;

use App\Http\Controllers\Controller;
use App\Services\Financial\ContasReceberService;
use App\DTOs\Financial\ContaReceberDTO;
use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class ContasReceberApiController extends Controller
{
    public function __construct(
        private ContasReceberService $contasReceberService
    ) {}

    /**
     * Listar contas a receber
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;

            $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->with(['cliente', 'funcionario', 'categoria', 'conta', 'pagamentos']);

            // Filtros
            if ($request->has('situacao')) {
                $query->where('situacao_financeira', $request->situacao);
            }

            if ($request->has('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            if ($request->has('data_inicio') && $request->has('data_fim')) {
                $query->whereBetween('data_vencimento', [
                    $request->data_inicio,
                    $request->data_fim
                ]);
            }

            if ($request->has('vencidas') && $request->vencidas) {
                $query->where('situacao_financeira', '!=', SituacaoFinanceiraEnum::PAGO)
                    ->where('data_vencimento', '<', now());
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('descricao', 'like', "%{$search}%")
                        ->orWhere('codigo_lancamento', 'like', "%{$search}%")
                        ->orWhereHas('cliente', function ($clienteQuery) use ($search) {
                            $clienteQuery->where('nome', 'like', "%{$search}%");
                        });
                });
            }

            $perPage = $request->get('per_page', 15);
            $contas = $query->orderBy('data_vencimento')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $contas->items(),
                'meta' => [
                    'current_page' => $contas->currentPage(),
                    'last_page' => $contas->lastPage(),
                    'per_page' => $contas->perPage(),
                    'total' => $contas->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas a receber: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Criar nova conta a receber
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'descricao' => 'required|string|max:255',
                'valor_total' => 'required|numeric|min:0.01',
                'data_vencimento' => 'required|date',
                'data_competencia' => 'nullable|date',
                'cliente_id' => 'nullable|integer|exists:pessoas,id',
                'funcionario_id' => 'nullable|integer|exists:users,id',
                'conta_gerencial_id' => 'nullable|integer|exists:contas_gerenciais,id',
                'categoria_id' => 'nullable|integer|exists:categorias,id',
                'observacoes' => 'nullable|string|max:1000',
                'codigo_lancamento' => 'nullable|string|max:100',
                'documento_referencia' => 'nullable|string|max:100',
                'cobranca_automatica' => 'nullable|boolean',
                'juros_multa_config' => 'nullable|string|max:500',
                'numero_parcelas' => 'nullable|integer|min:1|max:360',
                'frequencia_recorrencia' => 'nullable|in:MENSAL,QUINZENAL,SEMANAL,ANUAL',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $empresaId = auth()->user()->empresa_id ?? 1;
            $dados = $request->all();
            $dados['empresa_id'] = $empresaId;

            $dto = ContaReceberDTO::fromArray($dados);

            $validationErrors = $dto->validate();
            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos no DTO',
                    'errors' => $validationErrors,
                ], 422);
            }

            // Verificar se é parcelado
            if ($request->numero_parcelas > 1) {
                $contas = $this->contasReceberService->criarParcelado($dto);

                return response()->json([
                    'success' => true,
                    'message' => "Contas a receber criadas com sucesso ({$contas->count()} parcelas)",
                    'data' => $contas->load(['cliente', 'funcionario', 'categoria', 'conta']),
                ], 201);
            } else {
                $conta = $this->contasReceberService->criar($dto);

                return response()->json([
                    'success' => true,
                    'message' => 'Conta a receber criada com sucesso',
                    'data' => $conta->load(['cliente', 'funcionario', 'categoria', 'conta']),
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar conta a receber: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Exibir conta específica
     */
    public function show($id): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;

            $conta = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->where('id', $id)
                ->with(['cliente', 'funcionario', 'categoria', 'conta', 'pagamentos.formaPagamento', 'pagamentos.contaBancaria'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $conta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Conta a receber não encontrada',
            ], 404);
        }
    }

    /**
     * Atualizar conta a receber
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;

            $conta = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->where('id', $id)
                ->firstOrFail();

            if ($conta->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível editar uma conta já recebida',
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'descricao' => 'required|string|max:255',
                'valor_total' => 'required|numeric|min:0.01',
                'data_vencimento' => 'required|date',
                'data_competencia' => 'nullable|date',
                'cliente_id' => 'nullable|integer|exists:pessoas,id',
                'funcionario_id' => 'nullable|integer|exists:users,id',
                'conta_gerencial_id' => 'nullable|integer|exists:contas_gerenciais,id',
                'categoria_id' => 'nullable|integer|exists:categorias,id',
                'observacoes' => 'nullable|string|max:1000',
                'codigo_lancamento' => 'nullable|string|max:100',
                'documento_referencia' => 'nullable|string|max:100',
                'cobranca_automatica' => 'nullable|boolean',
                'juros_multa_config' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $conta->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Conta a receber atualizada com sucesso',
                'data' => $conta->load(['cliente', 'funcionario', 'categoria', 'conta', 'pagamentos']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar conta a receber: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Excluir conta a receber
     */
    public function destroy($id): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;

            $conta = LancamentoFinanceiro::where('empresa_id', $empresaId)
                ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
                ->where('id', $id)
                ->firstOrFail();

            if ($conta->pagamentos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir uma conta que possui recebimentos registrados',
                ], 422);
            }

            $conta->delete();

            return response()->json([
                'success' => true,
                'message' => 'Conta a receber excluída com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir conta a receber: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar recebimento
     */
    public function receber(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'data_pagamento' => 'required|date',
                'valor' => 'required|numeric|min:0.01',
                'forma_pagamento_id' => 'required|integer|exists:formas_pagamento,id',
                'conta_bancaria_id' => 'nullable|integer|exists:contas_bancarias,id',
                'valor_desconto' => 'nullable|numeric|min:0',
                'valor_juros' => 'nullable|numeric|min:0',
                'valor_multa' => 'nullable|numeric|min:0',
                'observacao' => 'nullable|string|max:1000',
                'comprovante_pagamento' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $dados = $validator->validated();
            $dados['usuario_id'] = auth()->id();

            $pagamento = $this->contasReceberService->receber($id, $dados);

            return response()->json([
                'success' => true,
                'message' => 'Recebimento registrado com sucesso',
                'data' => $pagamento->load(['lancamento', 'formaPagamento', 'contaBancaria']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar recebimento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Estornar recebimento
     */
    public function estornarRecebimento($pagamentoId): JsonResponse
    {
        try {
            $result = $this->contasReceberService->estornarRecebimento($pagamentoId);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recebimento estornado com sucesso',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao estornar recebimento',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao estornar recebimento: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dashboard de contas a receber
     */
    public function dashboard(): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;
            $dashboard = $this->contasReceberService->getDashboard($empresaId);

            return response()->json([
                'success' => true,
                'data' => $dashboard,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar dashboard: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Projeção de fluxo de caixa
     */
    public function projecaoFluxoCaixa(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'data_inicio' => 'required|date',
                'data_fim' => 'required|date|after_or_equal:data_inicio',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $empresaId = auth()->user()->empresa_id ?? 1;
            $dataInicio = Carbon::parse($request->data_inicio);
            $dataFim = Carbon::parse($request->data_fim);

            $projecao = $this->contasReceberService->getProjecaoFluxoCaixa($empresaId, $dataInicio, $dataFim);

            return response()->json([
                'success' => true,
                'data' => $projecao,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar projeção: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Contas vencendo
     */
    public function contasVencendo(Request $request): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;
            $diasAdiante = $request->get('dias', 7);

            $contas = $this->contasReceberService->getContasVencendo($empresaId, $diasAdiante);

            return response()->json([
                'success' => true,
                'data' => $contas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas vencendo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Contas vencidas
     */
    public function contasVencidas(): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;
            $contas = $this->contasReceberService->getContasVencidas($empresaId);

            return response()->json([
                'success' => true,
                'data' => $contas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas vencidas: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Processar cobrança automática
     */
    public function processarCobrancaAutomatica(): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id ?? 1;
            $resultado = $this->contasReceberService->processarCobrancaAutomatica($empresaId);

            return response()->json([
                'success' => true,
                'message' => "Cobrança automática processada. {$resultado['processadas']} contas processadas, {$resultado['erros']} erros.",
                'data' => $resultado,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar cobrança automática: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Relatório de recebimentos
     */
    public function relatorioRecebimentos(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'data_inicio' => 'required|date',
                'data_fim' => 'required|date|after_or_equal:data_inicio',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $empresaId = auth()->user()->empresa_id ?? 1;
            $dataInicio = Carbon::parse($request->data_inicio);
            $dataFim = Carbon::parse($request->data_fim);

            $relatorio = $this->contasReceberService->getRelatorioRecebimentos($empresaId, $dataInicio, $dataFim);

            return response()->json([
                'success' => true,
                'data' => $relatorio,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage(),
            ], 500);
        }
    }
}
