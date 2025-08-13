<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financial\ContaGerencialRequest;
use App\Services\Financial\ContaGerencialService;
use App\Services\Financial\CategoriaContaGerencialService;
use App\DTOs\Financial\ContaGerencialDTO;
use App\Models\Financial\ContaGerencial;
use App\Models\Financial\ClassificacaoDre;
use App\Models\Financial\Tipo;
use App\Enums\NaturezaContaEnum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContaGerencialController extends Controller
{
    public function __construct(
        private ContaGerencialService $service,
        private CategoriaContaGerencialService $categoriaService
    ) {}

    /**
     * Lista das contas gerenciais
     */
    public function index(Request $request, int $empresa): View|JsonResponse
    {
        $filtros = $request->only(['nome', 'ativo', 'categoria_id', 'natureza']);
        $filtros['empresa_id'] = $empresa;
        $contas = $this->service->index($filtros);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $contas->items(),
                'pagination' => [
                    'current_page' => $contas->currentPage(),
                    'last_page' => $contas->lastPage(),
                    'per_page' => $contas->perPage(),
                    'total' => $contas->total(),
                ],
            ]);
        }

        $categorias = $this->categoriaService->getParaSelecao($empresa);
        $naturezas = NaturezaContaEnum::cases();
        $contasParaPai = $this->service->getHierarquia(true, $empresa);

        return view('comerciantes.financeiro.contas.index', compact(
            'contas',
            'categorias',
            'naturezas',
            'contasParaPai',
            'filtros',
            'empresa'
        ));
    }

    /**
     * Formulário de criação
     */
    public function create(Request $request, int $empresa): View
    {
        return view('comerciantes.financeiro.contas.create', compact('empresa'));
    }

    /**
     * Armazena nova conta
     */
    public function store(ContaGerencialRequest $request, int $empresa): RedirectResponse|JsonResponse
    {
        try {
            $dto = ContaGerencialDTO::fromRequest($request);
            $dto->empresa_id = $empresa;
            $conta = $this->service->create($dto);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conta gerencial criada com sucesso!',
                    'data' => $conta,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id])
                ->with('success', 'Conta gerencial criada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar conta gerencial: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar conta gerencial: ' . $e->getMessage()]);
        }
    }

    /**
     * Exibe detalhes da conta
     */
    public function show(Request $request, int $empresa, int $id): View|JsonResponse
    {
        $conta = $this->service->find($id);

        if (!$conta) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conta gerencial não encontrada',
                ], 404);
            }

            abort(404, 'Conta gerencial não encontrada');
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $conta,
            ]);
        }

        return view('comerciantes.financeiro.contas.show', compact('conta', 'empresa'));
    }

    /**
     * Formulário de edição
     */
    public function edit(Request $request, int $empresa, int $id): View
    {
        $conta = $this->service->find($id);

        if (!$conta) {
            abort(404, 'Conta gerencial não encontrada');
        }

        $categorias = $this->categoriaService->getParaSelecao($empresa);
        $contasPai = $this->service->getHierarquia(true, $empresa);
        $classificacoesDre = ClassificacaoDre::ativos()->ordenado()->get();
        $tipos = Tipo::ativos()->ordenado()->get();
        $naturezas = NaturezaContaEnum::cases();

        return view('comerciantes.financeiro.contas.edit', compact(
            'conta',
            'categorias',
            'contasPai',
            'classificacoesDre',
            'tipos',
            'naturezas',
            'empresa'
        ));
    }

    /**
     * Atualiza conta existente
     */
    public function update(ContaGerencialRequest $request, int $empresa, int $id): RedirectResponse|JsonResponse
    {
        try {
            $dto = ContaGerencialDTO::fromRequest($request);
            $conta = $this->service->update($id, $dto);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conta gerencial atualizada com sucesso!',
                    'data' => $conta,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.contas.show', ['empresa' => $empresa, 'id' => $conta->id])
                ->with('success', 'Conta gerencial atualizada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar conta gerencial: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao atualizar conta gerencial: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove conta
     */
    public function destroy(Request $request, int $empresa, int $id): RedirectResponse|JsonResponse
    {
        try {
            $this->service->delete($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conta gerencial excluída com sucesso!',
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.contas.index', ['empresa' => $empresa])
                ->with('success', 'Conta gerencial excluída com sucesso!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir conta gerencial: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Erro ao excluir conta gerencial: ' . $e->getMessage()]);
        }
    }

    /**
     * Lista hierárquica para AJAX
     */
    public function hierarchy(Request $request, int $empresa): JsonResponse
    {
        try {
            $apenasAtivas = $request->boolean('apenas_ativas', true);
            $hierarquia = $this->service->getHierarquia($apenasAtivas, $empresa);

            return response()->json([
                'success' => true,
                'data' => $hierarquia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar hierarquia: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Lista contas para lançamento
     */
    public function forLaunch(Request $request, int $empresa): JsonResponse
    {
        try {
            $apenasAtivas = $request->boolean('apenas_ativas', true);
            $contas = $this->service->getContasParaLancamento($apenasAtivas, $empresa);

            return response()->json([
                'success' => true,
                'data' => $contas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Lista contas por categoria
     */
    public function byCategory(Request $request, int $empresa, int $categoriaId): JsonResponse
    {
        try {
            $apenasAtivas = $request->boolean('apenas_ativas', true);
            $contas = $this->service->getPorCategoria($categoriaId, $apenasAtivas, $empresa);

            return response()->json([
                'success' => true,
                'data' => $contas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas por categoria: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Lista contas por natureza
     */
    public function byNature(Request $request, int $empresa, string $natureza): JsonResponse
    {
        try {
            $naturezaEnum = NaturezaContaEnum::from($natureza);
            $apenasAtivas = $request->boolean('apenas_ativas', true);
            $contas = $this->service->getPorNatureza($naturezaEnum, $apenasAtivas, $empresa);

            return response()->json([
                'success' => true,
                'data' => $contas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas por natureza: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Importa plano de contas padrão
     */
    public function importDefault(Request $request, int $empresa): RedirectResponse|JsonResponse
    {
        try {
            $contasImportadas = $this->service->importarPlanoContasPadrao($empresa);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Plano de contas padrão importado com sucesso!',
                    'data' => $contasImportadas,
                ]);
            }

            return redirect()
                ->route('comerciantes.empresas.financeiro.contas.index', ['empresa' => $empresa])
                ->with('success', 'Plano de contas padrão importado com ' . count($contasImportadas) . ' contas!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao importar plano de contas: ' . $e->getMessage(),
                ], 422);
            }

            return back()
                ->withErrors(['error' => 'Erro ao importar plano de contas: ' . $e->getMessage()]);
        }
    }
}
