<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Services\Financial\ClassificacaoDreService;
use App\DTOs\Financial\ClassificacaoDreDTO;
use App\Http\Requests\Financial\ClassificacaoDreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassificacaoDreController extends Controller
{
    public function __construct(
        private ClassificacaoDreService $classificacaoService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id;
            
            if ($request->boolean('arvore')) {
                $classificacoes = $this->classificacaoService->obterArvoreClassificacoes(
                    $empresaId,
                    $request->boolean('apenas_ativas', true)
                );
            } else {
                $query = \App\Models\Financial\ClassificacaoDre::where('empresa_id', $empresaId)
                    ->with(['tipo', 'classificacaoPai']);

                if ($request->boolean('apenas_ativas', true)) {
                    $query->ativas();
                }

                if ($request->filled('tipo_id')) {
                    $query->porTipo($request->tipo_id);
                }

                $classificacoes = $query->orderBy('ordem_exibicao')->paginate(15);
            }

            return response()->json([
                'success' => true,
                'data' => $classificacoes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ClassificacaoDreRequest $request): JsonResponse
    {
        try {
            $dados = ClassificacaoDreDTO::fromArray($request->validated());
            $classificacao = $this->classificacaoService->criar($dados);

            return response()->json([
                'success' => true,
                'data' => $classificacao,
                'message' => 'Classificação DRE criada com sucesso'
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
            $classificacao = \App\Models\Financial\ClassificacaoDre::with([
                'tipo',
                'classificacaoPai',
                'classificacoesFilhas',
                'contasGerenciais'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $classificacao
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Classificação não encontrada'
            ], 404);
        }
    }

    public function update(ClassificacaoDreRequest $request, int $id): JsonResponse
    {
        try {
            $dados = ClassificacaoDreDTO::fromArray($request->validated());
            $classificacao = $this->classificacaoService->atualizar($id, $dados);

            return response()->json([
                'success' => true,
                'data' => $classificacao,
                'message' => 'Classificação DRE atualizada com sucesso'
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
            $classificacao = \App\Models\Financial\ClassificacaoDre::findOrFail($id);
            $classificacao->desativar();

            return response()->json([
                'success' => true,
                'message' => 'Classificação DRE desativada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function relatorioDre(Request $request): JsonResponse
    {
        try {
            $empresaId = auth()->user()->empresa_id;
            $dataInicio = $request->filled('data_inicio') ? new \DateTime($request->data_inicio) : null;
            $dataFim = $request->filled('data_fim') ? new \DateTime($request->data_fim) : null;

            $relatorio = $this->classificacaoService->gerarRelatorioDre(
                $empresaId,
                $dataInicio,
                $dataFim
            );

            return response()->json([
                'success' => true,
                'data' => $relatorio
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}