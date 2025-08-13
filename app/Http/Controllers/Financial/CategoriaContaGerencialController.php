<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\CategoriaContaGerencialRequest;
use App\Services\Financial\CategoriaContaGerencialService;
use App\DTOs\Financial\CategoriaContaGerencialDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoriaContaGerencialController extends Controller
{
    public function __construct(
        private CategoriaContaGerencialService $service
    ) {}

    /**
     * Lista das categorias
     */
    public function index(Request $request, int $empresa): View|JsonResponse
    {
        $filtros = $request->only(['nome', 'ativo', 'tipo']);
        $filtros['empresa_id'] = $empresa;
        $categorias = $this->service->index($filtros);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $categorias->items(),
                'pagination' => [
                    'current_page' => $categorias->currentPage(),
                    'last_page' => $categorias->lastPage(),
                    'per_page' => $categorias->perPage(),
                    'total' => $categorias->total(),
                ],
            ]);
        }

        $estatisticas = $this->service->getEstatisticas();

        return view('comerciantes.financeiro.categorias.index', compact(
            'categorias',
            'estatisticas',
            'filtros',
            'empresa'
        ));
    }

    /**
     * Formulário de criação
     */
    public function create(Request $request, int $empresa): View
    {
        return view('comerciantes.financeiro.categorias.create', compact('empresa'));
    }

    /**
     * Armazena nova categoria
     */
    public function store(CategoriaContaGerencialRequest $request, int $empresa): RedirectResponse|JsonResponse
    {
        try {
            $dto = CategoriaContaGerencialDTO::fromRequest($request);
            $dto->empresa_id = $empresa;
            $categoria = $this->service->create($dto);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria criada com sucesso!',
                    'data' => $categoria,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.categorias.show', ['empresa' => $empresa, 'id' => $categoria->id])
                ->with('success', 'Categoria criada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar categoria: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar categoria: ' . $e->getMessage()]);
        }
    }

    /**
     * Exibe detalhes da categoria
     */
    public function show(Request $request, int $empresa, int $id): View|JsonResponse
    {
        $categoria = $this->service->find($id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $categoria,
            ]);
        }

        return view('comerciantes.financeiro.categorias.show', compact('categoria', 'empresa'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Request $request, int $empresa, int $id): View
    {
        $categoria = $this->service->find($id);
        return view('comerciantes.financeiro.categorias.edit', compact('categoria', 'empresa'));
    }

    public function update(CategoriaContaGerencialRequest $request, int $empresa, int $id): RedirectResponse|JsonResponse
    {
        try {
            $dto = CategoriaContaGerencialDTO::fromRequest($request);
            $dto->empresa_id = $empresa;
            $categoria = $this->service->update($id, $dto);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria atualizada com sucesso!',
                    'data' => $categoria,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.categorias.show', ['empresa' => $empresa, 'id' => $categoria->id])
                ->with('success', 'Categoria atualizada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar categoria: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar categoria: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, int $empresa, int $id): RedirectResponse|JsonResponse
    {
        try {
            $this->service->delete($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria excluída com sucesso!',
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.categorias.index', ['empresa' => $empresa])
                ->with('success', 'Categoria excluída com sucesso!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir categoria: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Erro ao excluir categoria: ' . $e->getMessage()]);
        }
    }

    // Rotas especiais

    /**
     * Busca categorias por tipo
     */
    public function byType(Request $request, int $empresa, string $tipo): JsonResponse
    {
        $filtros = ['tipo' => $tipo, 'empresa_id' => $empresa];
        $categorias = $this->service->index($filtros);

        return response()->json([
            'success' => true,
            'data' => $categorias->items(),
        ]);
    }

    /**
     * Categorias para seleção em formulários
     */
    public function forSelection(Request $request, int $empresa): JsonResponse
    {
        $filtros = ['ativo' => 'true', 'empresa_id' => $empresa];
        $categorias = $this->service->index($filtros);

        return response()->json([
            'success' => true,
            'data' => $categorias->items()->map(function ($categoria) {
                return [
                    'id' => $categoria->id,
                    'nome' => $categoria->nome_completo,
                    'tipo' => $categoria->tipo->value,
                ];
            }),
        ]);
    }

    /**
     * Duplicar categoria
     */
    public function duplicate(Request $request, int $empresa, int $id): RedirectResponse|JsonResponse
    {
        try {
            $categoria = $this->service->duplicate($id);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoria duplicada com sucesso!',
                    'data' => $categoria,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.categorias.show', ['empresa' => $empresa, 'id' => $categoria->id])
                ->with('success', 'Categoria duplicada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao duplicar categoria: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Erro ao duplicar categoria: ' . $e->getMessage()]);
        }
    }

    /**
     * Importar categorias padrão
     */
    public function importDefault(Request $request, int $empresa): RedirectResponse|JsonResponse
    {
        try {
            $categorias = $this->service->importarCategoriasPadrao($empresa);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categorias padrão importadas com sucesso!',
                    'data' => $categorias,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.categorias.index', ['empresa' => $empresa])
                ->with('success', 'Categorias padrão importadas com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao importar categorias: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Erro ao importar categorias: ' . $e->getMessage()]);
        }
    }

    /**
     * Estatísticas das categorias
     */
    public function statistics(Request $request, int $empresa): JsonResponse
    {
        $estatisticas = $this->service->getEstatisticasPorEmpresa($empresa);

        return response()->json([
            'success' => true,
            'data' => $estatisticas,
        ]);
    }
}
