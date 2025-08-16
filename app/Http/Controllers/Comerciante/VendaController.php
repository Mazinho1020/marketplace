<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendas\StoreVendaRequest;
use App\Http\Requests\Vendas\UpdateVendaRequest;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\Cliente;
use App\Services\Vendas\VendaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

/**
 * Controller para gerenciar vendas no painel do comerciante
 * 
 * Segue o padrão definido em PADRONIZACAO_COMPLETA.md
 */
class VendaController extends Controller
{
    protected VendaService $vendaService;

    public function __construct(VendaService $vendaService)
    {
        $this->vendaService = $vendaService;
    }

    /**
     * Lista as vendas da empresa
     */
    public function index(Request $request): View
    {
        $filtros = $request->only([
            'status', 'cliente_id', 'vendedor_id', 'data_inicio', 
            'data_fim', 'tipo_venda', 'numero_venda'
        ]);
        
        // Adicionar empresa do usuário logado
        $filtros['empresa_id'] = auth()->user()->empresa_id ?? 1; // Temporário até implementar multitenancy completo
        
        $vendas = $this->vendaService->buscar($filtros, $request->get('per_page', 15));
        
        // Dados para filtros
        $statusOptions = Venda::STATUS;
        $tiposVenda = Venda::TIPOS_VENDA;
        $clientes = Cliente::where('empresa_id', $filtros['empresa_id'])->select('id', 'nome')->get();
        
        return view('comerciantes.vendas.index', compact(
            'vendas', 'filtros', 'statusOptions', 'tiposVenda', 'clientes'
        ));
    }

    /**
     * Mostra o formulário para criar uma nova venda
     */
    public function create(): View
    {
        $empresaId = auth()->user()->empresa_id ?? 1; // Temporário
        
        $produtos = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->with(['categoria', 'imagens'])
            ->select('id', 'nome', 'preco_venda', 'estoque_atual', 'controla_estoque', 'categoria_id')
            ->get();
            
        $clientes = Cliente::where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->select('id', 'nome', 'telefone', 'email')
            ->orderBy('nome')
            ->get();
        
        $tiposVenda = Venda::TIPOS_VENDA;
        $origensVenda = Venda::ORIGENS_VENDA;
        $tiposEntrega = Venda::TIPOS_ENTREGA;
        
        return view('comerciantes.vendas.create', compact(
            'produtos', 'clientes', 'tiposVenda', 'origensVenda', 'tiposEntrega'
        ));
    }

    /**
     * Armazena uma nova venda
     */
    public function store(StoreVendaRequest $request): JsonResponse|RedirectResponse
    {
        try {
            // Adicionar empresa do usuário logado
            $dados = $request->validated();
            $dados['empresa_id'] = auth()->user()->empresa_id ?? 1; // Temporário
            
            $venda = $this->vendaService->criar($dados);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda criada com sucesso!',
                    'data' => $venda->load(['itens', 'cliente'])
                ], 201);
            }
            
            return redirect()
                ->route('comerciantes.vendas.show', $venda)
                ->with('success', 'Venda criada com sucesso!');
                
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar venda: ' . $e->getMessage()
                ], 422);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar venda: ' . $e->getMessage());
        }
    }

    /**
     * Exibe uma venda específica
     */
    public function show(Venda $venda): View
    {
        $venda->load(['itens.produto', 'cliente', 'vendedor', 'pagamentos']);
        
        return view('comerciantes.vendas.show', compact('venda'));
    }

    /**
     * Mostra o formulário para editar uma venda
     */
    public function edit(Venda $venda): View
    {
        // Só permite editar vendas abertas
        if ($venda->status !== 'aberta') {
            abort(403, 'Apenas vendas abertas podem ser editadas.');
        }
        
        $venda->load(['itens.produto', 'cliente']);
        
        $empresaId = $venda->empresa_id;
        
        $produtos = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->with(['categoria', 'imagens'])
            ->select('id', 'nome', 'preco_venda', 'estoque_atual', 'controla_estoque', 'categoria_id')
            ->get();
            
        $clientes = Cliente::where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->select('id', 'nome', 'telefone', 'email')
            ->orderBy('nome')
            ->get();
        
        $tiposVenda = Venda::TIPOS_VENDA;
        $origensVenda = Venda::ORIGENS_VENDA;
        $tiposEntrega = Venda::TIPOS_ENTREGA;
        
        return view('comerciantes.vendas.edit', compact(
            'venda', 'produtos', 'clientes', 'tiposVenda', 'origensVenda', 'tiposEntrega'
        ));
    }

    /**
     * Atualiza uma venda
     */
    public function update(UpdateVendaRequest $request, Venda $venda): JsonResponse|RedirectResponse
    {
        try {
            $dados = $request->validated();
            $vendaAtualizada = $this->vendaService->atualizar($venda, $dados);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda atualizada com sucesso!',
                    'data' => $vendaAtualizada->load(['itens', 'cliente'])
                ]);
            }
            
            return redirect()
                ->route('comerciantes.vendas.show', $vendaAtualizada)
                ->with('success', 'Venda atualizada com sucesso!');
                
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar venda: ' . $e->getMessage()
                ], 422);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma venda (soft delete)
     */
    public function destroy(Venda $venda): JsonResponse|RedirectResponse
    {
        try {
            // Só permite excluir vendas abertas
            if ($venda->status !== 'aberta') {
                throw new Exception('Apenas vendas abertas podem ser excluídas.');
            }
            
            $venda->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda excluída com sucesso!'
                ]);
            }
            
            return redirect()
                ->route('comerciantes.vendas.index')
                ->with('success', 'Venda excluída com sucesso!');
                
        } catch (Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao excluir venda: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Erro ao excluir venda: ' . $e->getMessage());
        }
    }

    /**
     * Finaliza uma venda
     */
    public function finalizar(Request $request, Venda $venda): JsonResponse|RedirectResponse
    {
        try {
            $opcoes = $request->only(['pagamentos']);
            $vendaFinalizada = $this->vendaService->finalizar($venda, $opcoes);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda finalizada com sucesso!',
                    'data' => $vendaFinalizada
                ]);
            }
            
            return redirect()
                ->route('comerciantes.vendas.show', $vendaFinalizada)
                ->with('success', 'Venda finalizada com sucesso!');
                
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao finalizar venda: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Erro ao finalizar venda: ' . $e->getMessage());
        }
    }

    /**
     * Cancela uma venda
     */
    public function cancelar(Request $request, Venda $venda): JsonResponse|RedirectResponse
    {
        $request->validate([
            'motivo' => 'required|string|max:255'
        ]);
        
        try {
            $usuarioId = auth()->id();
            $vendaCancelada = $this->vendaService->cancelar($venda, $request->motivo, $usuarioId);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Venda cancelada com sucesso!',
                    'data' => $vendaCancelada
                ]);
            }
            
            return redirect()
                ->route('comerciantes.vendas.show', $vendaCancelada)
                ->with('success', 'Venda cancelada com sucesso!');
                
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cancelar venda: ' . $e->getMessage()
                ], 422);
            }
            
            return back()->with('error', 'Erro ao cancelar venda: ' . $e->getMessage());
        }
    }

    /**
     * Adiciona um item à venda
     */
    public function adicionarItem(Request $request, Venda $venda): JsonResponse
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|numeric|min:0.001',
            'valor_unitario' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string|max:500'
        ]);
        
        try {
            $item = $this->vendaService->adicionarItem($venda, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Item adicionado com sucesso!',
                'data' => $item->load('produto')
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar item: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove um item da venda
     */
    public function removerItem(Venda $venda, int $itemId): JsonResponse
    {
        try {
            $this->vendaService->removerItem($venda, $itemId);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removido com sucesso!'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover item: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Obtém estatísticas de vendas
     */
    public function estatisticas(Request $request): JsonResponse
    {
        $filtros = $request->only(['data_inicio', 'data_fim']);
        $empresaId = auth()->user()->empresa_id ?? 1; // Temporário
        
        try {
            $estatisticas = $this->vendaService->obterEstatisticas($empresaId, $filtros);
            
            return response()->json([
                'success' => true,
                'data' => $estatisticas
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca produtos para venda (AJAX)
     */
    public function buscarProdutos(Request $request): JsonResponse
    {
        $empresaId = auth()->user()->empresa_id ?? 1; // Temporário
        $termo = $request->get('q', '');
        
        $produtos = Produto::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->where(function ($query) use ($termo) {
                $query->where('nome', 'like', "%{$termo}%")
                    ->orWhere('sku', 'like', "%{$termo}%")
                    ->orWhere('codigo_sistema', 'like', "%{$termo}%");
            })
            ->select('id', 'nome', 'sku', 'preco_venda', 'estoque_atual', 'controla_estoque')
            ->limit(20)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $produtos
        ]);
    }

    /**
     * Busca clientes para venda (AJAX)
     */
    public function buscarClientes(Request $request): JsonResponse
    {
        $empresaId = auth()->user()->empresa_id ?? 1; // Temporário
        $termo = $request->get('q', '');
        
        $clientes = Cliente::where('empresa_id', $empresaId)
            ->where('status', 'ativo')
            ->where(function ($query) use ($termo) {
                $query->where('nome', 'like', "%{$termo}%")
                    ->orWhere('telefone', 'like', "%{$termo}%")
                    ->orWhere('email', 'like', "%{$termo}%")
                    ->orWhere('cpf_cnpj', 'like', "%{$termo}%");
            })
            ->select('id', 'nome', 'telefone', 'email', 'cpf_cnpj')
            ->limit(20)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $clientes
        ]);
    }
}
