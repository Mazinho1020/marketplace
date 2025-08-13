<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Services\Financial\ContaGerencialService;
use App\DTOs\Financial\ContaGerencialDTO;
use App\Http\Requests\Financial\ContaGerencialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContaGerencialController extends Controller
{
    public function __construct(
        private ContaGerencialService $contaGerencialService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id;
            
            if ($request->boolean('arvore')) {
                $contas = $this->contaGerencialService->obterArvoreContas(
                    $empresaId,
                    $request->boolean('apenas_ativas', true)
                );
            } else {
                $query = \App\Models\Financial\ContaGerencial::where('empresa_id', $empresaId)
                    ->with(['classificacaoDre', 'tipo', 'naturezas']);

                if ($request->boolean('apenas_ativas', true)) {
                    $query->ativas();
                }

                if ($request->filled('classificacao_id')) {
                    $query->porClassificacao($request->classificacao_id);
                }

                if ($request->filled('tipo_id')) {
                    $query->porTipo($request->tipo_id);
                }

                $contas = $query->orderBy('nome')->paginate(15);
            }

            return response()->json([
                'success' => true,
                'data' => $contas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ContaGerencialRequest $request): JsonResponse
    {
        try {
            $dados = ContaGerencialDTO::fromArray($request->validated());
            $conta = $this->contaGerencialService->criar($dados);

            return response()->json([
                'success' => true,
                'data' => $conta,
                'message' => 'Conta gerencial criada com sucesso'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $conta = \App\Models\Financial\ContaGerencial::with([
                'classificacaoDre',
                'tipo',
                'naturezas',
                'contasFilhas'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $conta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Conta não encontrada'
            ], 404);
        }
    }

    public function update(ContaGerencialRequest $request, int $id): JsonResponse
    {
        try {
            $dados = ContaGerencialDTO::fromArray($request->validated());
            $conta = $this->contaGerencialService->atualizar($id, $dados);

            return response()->json([
                'success' => true,
                'data' => $conta,
                'message' => 'Conta gerencial atualizada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $conta = \App\Models\Financial\ContaGerencial::findOrFail($id);
            $conta->desativar();

            return response()->json([
                'success' => true,
                'message' => 'Conta gerencial desativada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function paraLancamento(): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id;
            $contas = $this->contaGerencialService->obterContasParaLancamento($empresaId);

            return response()->json([
                'success' => true,
                'data' => $contas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resumoFinanceiro(Request $request): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id;
            $dataInicio = $request->filled('data_inicio') ? new \DateTime($request->data_inicio) : null;
            $dataFim = $request->filled('data_fim') ? new \DateTime($request->data_fim) : null;

            $resumo = $this->contaGerencialService->calcularResumoFinanceiro(
                $empresaId,
                $dataInicio,
                $dataFim
            );

            return response()->json([
                'success' => true,
                'data' => $resumo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}