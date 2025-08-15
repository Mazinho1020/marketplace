<?php

namespace App\Http\Controllers\API\Financial;

use App\Http\Controllers\Controller;
use App\Services\Financial\ContasPagarService;
use App\DTOs\Financial\ContaPagarDTO;
use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ContasPagarApiController extends Controller
{
    public function __construct(
        private ContasPagarService $contasPagarService
    ) {}

    /**
     * GET /api/contas-pagar
     * Listar contas a pagar com filtros
     */
    public function index(Request $request): JsonResponse
    {
        $empresaId = $request->user()->empresa_id ?? $request->get('empresa_id');

        if (!$empresaId) {
            return response()->json(['error' => 'Empresa ID é obrigatório'], 400);
        }

        $query = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('natureza_financeira', 'pagar')
            ->with(['pessoa', 'contaGerencial', 'pagamentos']);

        // Aplicar filtros
        if ($request->filled('situacao')) {
            $query->where('situacao_financeira', $request->situacao);
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_vencimento', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('data_vencimento', '<=', $request->data_fim);
        }

        if ($request->filled('pessoa_id')) {
            $query->where('pessoa_id', $request->pessoa_id);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('descricao', 'like', "%{$busca}%")
                    ->orWhere('numero_documento', 'like', "%{$busca}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'data_vencimento');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = min($request->get('per_page', 15), 100);
        $contas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $contas->items(),
            'meta' => [
                'current_page' => $contas->currentPage(),
                'last_page' => $contas->lastPage(),
                'per_page' => $contas->perPage(),
                'total' => $contas->total(),
            ]
        ]);
    }

    /**
     * POST /api/contas-pagar
     * Criar nova conta a pagar
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => 'required|integer|exists:empresas,id',
            'descricao' => 'required|string|max:255',
            'valor_original' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'pessoa_id' => 'nullable|integer',
            'pessoa_tipo' => 'nullable|in:funcionario,fornecedor,cliente',
            'conta_gerencial_id' => 'nullable|integer',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
            'parcelado' => 'boolean',
            'parcelas' => 'nullable|required_if:parcelado,true|integer|min:2|max:360',
            'intervalo_parcelas' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dados = ContaPagarDTO::fromArray($request->all());

            if ($request->boolean('parcelado') && $request->filled('parcelas')) {
                // Criar conta parcelada
                $contas = $this->contasPagarService->criarParcelado(
                    $dados,
                    $request->integer('parcelas')
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Contas a pagar parceladas criadas com sucesso',
                    'data' => $contas,
                ], 201);
            } else {
                // Criar conta única
                $conta = $this->contasPagarService->criar($dados);

                return response()->json([
                    'success' => true,
                    'message' => 'Conta a pagar criada com sucesso',
                    'data' => $conta,
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-pagar/{id}
     * Detalhar conta a pagar
     */
    public function show(int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::with([
                'pessoa',
                'contaGerencial',
                'pagamentos',
                'parcelasRelacionadas'
            ])->findOrFail($id);

            if ($conta->natureza_financeira !== 'pagar') {
                return response()->json([
                    'success' => false,
                    'error' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $conta
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Conta a pagar não encontrada'
            ], 404);
        }
    }

    /**
     * PUT /api/contas-pagar/{id}
     * Atualizar conta a pagar
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'sometimes|required|string|max:255',
            'valor_original' => 'sometimes|required|numeric|min:0.01',
            'data_vencimento' => 'sometimes|required|date',
            'pessoa_id' => 'nullable|integer',
            'conta_gerencial_id' => 'nullable|integer',
            'numero_documento' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if ($conta->natureza_financeira !== 'pagar') {
                return response()->json([
                    'success' => false,
                    'error' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            if ($conta->situacao_financeira === 'pago') {
                return response()->json([
                    'success' => false,
                    'error' => 'Não é possível alterar uma conta já paga'
                ], 400);
            }

            $conta->update($request->only([
                'descricao',
                'valor_original',
                'data_vencimento',
                'pessoa_id',
                'conta_gerencial_id',
                'numero_documento',
                'observacoes'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Conta a pagar atualizada com sucesso',
                'data' => $conta->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/contas-pagar/{id}/pagar
     * Efetuar pagamento
     */
    public function pagar(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'forma_pagamento_id' => 'required|integer',
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'conta_bancaria_id' => 'nullable|integer',
            'observacao' => 'nullable|string|max:500',
            'comprovante_pagamento' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pagamento = $this->contasPagarService->pagar($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pagamento registrado com sucesso',
                'data' => $pagamento
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/contas-pagar/{id}/pagamentos
     * Listar pagamentos de uma conta
     */
    public function pagamentos(int $id): JsonResponse
    {
        try {
            $conta = LancamentoFinanceiro::findOrFail($id);

            if ($conta->natureza_financeira !== 'pagar') {
                return response()->json([
                    'success' => false,
                    'error' => 'Este lançamento não é uma conta a pagar'
                ], 400);
            }

            $pagamentos = $conta->pagamentos()
                ->with(['formaPagamento'])
                ->orderBy('data_pagamento', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pagamentos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Conta a pagar não encontrada'
            ], 404);
        }
    }

    /**
     * DELETE /api/contas-pagar/pagamentos/{id}
     * Estornar pagamento
     */
    public function estornarPagamento(Request $request, int $pagamentoId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'motivo' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $this->contasPagarService->estornarPagamento(
                $pagamentoId,
                $request->motivo
            );

            return response()->json([
                'success' => true,
                'message' => 'Pagamento estornado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/contas-pagar/dashboard
     * Dashboard de contas a pagar
     */
    public function dashboard(Request $request): JsonResponse
    {
        $empresaId = $request->user()->empresa_id ?? $request->get('empresa_id');

        if (!$empresaId) {
            return response()->json(['error' => 'Empresa ID é obrigatório'], 400);
        }

        try {
            $dashboard = $this->contasPagarService->getDashboard($empresaId);

            return response()->json([
                'success' => true,
                'data' => $dashboard
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-pagar/vencidas
     * Obter contas vencidas
     */
    public function vencidas(Request $request): JsonResponse
    {
        $empresaId = $request->user()->empresa_id ?? $request->get('empresa_id');

        if (!$empresaId) {
            return response()->json(['error' => 'Empresa ID é obrigatório'], 400);
        }

        try {
            $contasVencidas = $this->contasPagarService->getVencidas($empresaId);

            return response()->json([
                'success' => true,
                'data' => $contasVencidas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/contas-pagar/projecao-fluxo
     * Projeção de fluxo de caixa
     */
    public function projecaoFluxo(Request $request): JsonResponse
    {
        $empresaId = $request->user()->empresa_id ?? $request->get('empresa_id');
        $dias = $request->get('dias', 30);

        if (!$empresaId) {
            return response()->json(['error' => 'Empresa ID é obrigatório'], 400);
        }

        try {
            $projecao = $this->contasPagarService->getProjecaoFluxoCaixa($empresaId, $dias);

            return response()->json([
                'success' => true,
                'data' => $projecao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
