<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoConfiguracao;
use App\Models\ProdutoConfiguracaoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdutoConfiguracaoController extends Controller
{
    /**
     * Lista todas as configurações de produtos
     */
    public function index(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $query = ProdutoConfiguracao::with(['produto', 'itens'])
            ->porEmpresa($empresaId);

        // Filtros
        if ($request->filled('tipo_configuracao')) {
            $query->where('tipo_configuracao', $request->tipo_configuracao);
        }

        if ($request->filled('produto_id')) {
            $query->where('produto_id', $request->produto_id);
        }

        if ($request->filled('status')) {
            $query->where('ativo', $request->status === 'ativo');
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        $configuracoes = $query->orderBy('nome')->paginate(15);

        // Para os filtros
        $produtos = Produto::porEmpresa($empresaId)->orderBy('nome')->get();

        // Tipos de configuração disponíveis
        $tiposConfiguracao = [
            'tamanho' => 'Tamanho',
            'sabor' => 'Sabor',
            'ingrediente' => 'Ingrediente',
            'complemento' => 'Complemento',
            'personalizado' => 'Personalizado'
        ];

        return view('comerciantes.produtos.configuracoes.index', compact(
            'configuracoes',
            'produtos',
            'tiposConfiguracao'
        ));
    }

    /**
     * Exibe formulário de criação
     */
    public function create()
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $produtos = Produto::porEmpresa($empresaId)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.configuracoes.create', compact('produtos'));
    }

    /**
     * Armazena nova configuração
     */
    public function store(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|in:tamanho,sabor,ingrediente,complemento,borda,temperatura,personalizacao,outro',
            'produto_id' => 'nullable|exists:produtos,id',
            'descricao' => 'nullable|string',
            'obrigatorio' => 'boolean',
            'multipla_selecao' => 'boolean',
            'max_selecoes' => 'nullable|integer|min:1',
            'ativo' => 'boolean',
            'itens' => 'required|array|min:1',
            'itens.*.nome' => 'required|string|max:255',
            'itens.*.preco_adicional' => 'nullable|numeric|min:0',
            'itens.*.descricao' => 'nullable|string',
            'itens.*.ordem' => 'nullable|integer|min:1',
            'itens.*.ativo' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Criar configuração
            $configuracao = ProdutoConfiguracao::create([
                'empresa_id' => $empresaId,
                'produto_id' => $request->produto_id ?: null,
                'nome' => $request->nome,
                'tipo' => $request->tipo,
                'descricao' => $request->descricao,
                'obrigatorio' => $request->boolean('obrigatorio'),
                'multipla_selecao' => $request->boolean('multipla_selecao'),
                'max_selecoes' => $request->multipla_selecao ? $request->max_selecoes : null,
                'ativo' => $request->boolean('ativo', true),
            ]);

            // Criar itens
            foreach ($request->itens as $itemData) {
                if (!empty($itemData['nome'])) {
                    ProdutoConfiguracaoItem::create([
                        'produto_configuracao_id' => $configuracao->id,
                        'nome' => $itemData['nome'],
                        'preco_adicional' => $itemData['preco_adicional'] ?? 0,
                        'descricao' => $itemData['descricao'] ?? null,
                        'ordem' => $itemData['ordem'] ?? 1,
                        'ativo' => isset($itemData['ativo']) && $itemData['ativo'] ? true : false,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.configuracoes.show', $configuracao)
                ->with('success', 'Configuração criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erro ao criar configuração: ' . $e->getMessage());
        }
    }

    /**
     * Exibe uma configuração específica
     */
    public function show(ProdutoConfiguracao $configuracao)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            abort(404);
        }

        $configuracao->load(['produto', 'itens' => function ($query) {
            $query->orderBy('ordem');
        }]);

        return view('comerciantes.produtos.configuracoes.show', compact('configuracao'));
    }

    /**
     * Exibe formulário de edição
     */
    public function edit(ProdutoConfiguracao $configuracao)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            abort(404);
        }

        $configuracao->load(['itens' => function ($query) {
            $query->orderBy('ordem');
        }]);

        $produtos = Produto::porEmpresa($empresaId)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.configuracoes.edit', compact('configuracao', 'produtos'));
    }

    /**
     * Atualiza uma configuração
     */
    public function update(Request $request, ProdutoConfiguracao $configuracao)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            abort(404);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|string|in:tamanho,sabor,ingrediente,complemento,borda,temperatura,personalizacao,outro',
            'produto_id' => 'nullable|exists:produtos,id',
            'descricao' => 'nullable|string',
            'obrigatorio' => 'boolean',
            'multipla_selecao' => 'boolean',
            'max_selecoes' => 'nullable|integer|min:1',
            'ativo' => 'boolean',
            'itens' => 'required|array|min:1',
            'itens.*.id' => 'nullable|exists:produto_configuracao_itens,id',
            'itens.*.nome' => 'required|string|max:255',
            'itens.*.preco_adicional' => 'nullable|numeric|min:0',
            'itens.*.descricao' => 'nullable|string',
            'itens.*.ordem' => 'nullable|integer|min:1',
            'itens.*.ativo' => 'boolean',
            'itens.*._destroy' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Atualizar configuração
            $configuracao->update([
                'produto_id' => $request->produto_id ?: null,
                'nome' => $request->nome,
                'tipo' => $request->tipo,
                'descricao' => $request->descricao,
                'obrigatorio' => $request->boolean('obrigatorio'),
                'multipla_selecao' => $request->boolean('multipla_selecao'),
                'max_selecoes' => $request->multipla_selecao ? $request->max_selecoes : null,
                'ativo' => $request->boolean('ativo', true),
            ]);

            // Processar itens
            foreach ($request->itens as $itemData) {
                if (!empty($itemData['nome'])) {
                    // Se marcado para destruir, pular
                    if (isset($itemData['_destroy']) && $itemData['_destroy']) {
                        if (isset($itemData['id'])) {
                            ProdutoConfiguracaoItem::find($itemData['id'])?->delete();
                        }
                        continue;
                    }

                    if (isset($itemData['id']) && $itemData['id']) {
                        // Atualizar item existente
                        $item = ProdutoConfiguracaoItem::find($itemData['id']);
                        if ($item && $item->produto_configuracao_id === $configuracao->id) {
                            $item->update([
                                'nome' => $itemData['nome'],
                                'preco_adicional' => $itemData['preco_adicional'] ?? 0,
                                'descricao' => $itemData['descricao'] ?? null,
                                'ordem' => $itemData['ordem'] ?? 1,
                                'ativo' => isset($itemData['ativo']) && $itemData['ativo'] ? true : false,
                            ]);
                        }
                    } else {
                        // Criar novo item
                        ProdutoConfiguracaoItem::create([
                            'produto_configuracao_id' => $configuracao->id,
                            'nome' => $itemData['nome'],
                            'preco_adicional' => $itemData['preco_adicional'] ?? 0,
                            'descricao' => $itemData['descricao'] ?? null,
                            'ordem' => $itemData['ordem'] ?? 1,
                            'ativo' => isset($itemData['ativo']) && $itemData['ativo'] ? true : false,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.configuracoes.show', $configuracao)
                ->with('success', 'Configuração atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Erro ao atualizar configuração: ' . $e->getMessage());
        }
    }

    /**
     * Remove uma configuração
     */
    public function destroy(ProdutoConfiguracao $configuracao)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            // Excluir itens primeiro
            $configuracao->itens()->delete();

            // Excluir configuração
            $configuracao->delete();

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.configuracoes.index')
                ->with('success', 'Configuração excluída com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->with('error', 'Erro ao excluir configuração: ' . $e->getMessage());
        }
    }

    /**
     * Busca configurações por produto (AJAX)
     */
    public function porProduto(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $configuracoes = ProdutoConfiguracao::with(['itens'])
            ->porEmpresa($empresaId)
            ->where('ativo', true);

        if ($request->filled('produto_id')) {
            $configuracoes->where(function ($query) use ($request) {
                $query->where('produto_id', $request->produto_id)
                    ->orWhereNull('produto_id');
            });
        }

        return response()->json([
            'configuracoes' => $configuracoes->get()
        ]);
    }

    /**
     * Toggle do status ativo/inativo (AJAX)
     */
    public function toggleAtivo(ProdutoConfiguracao $configuracao)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        try {
            $configuracao->update(['ativo' => !$configuracao->ativo]);

            return response()->json([
                'success' => true,
                'ativo' => $configuracao->ativo,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Adicionar item à configuração
     */
    public function storeItem(Request $request, ProdutoConfiguracao $configuracao)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            return redirect()->back()->with('error', 'Não autorizado');
        }

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor_adicional' => 'nullable|numeric|min:0',
            'ordem' => 'nullable|integer|min:0',
            'disponivel' => 'boolean',
            'padrao' => 'boolean'
        ]);

        try {
            $configuracao->itens()->create([
                'empresa_id' => $empresaId,
                'nome' => $validated['nome'],
                'descricao' => $validated['descricao'] ?? null,
                'valor_adicional' => $validated['valor_adicional'] ?? 0.00,
                'ordem' => $validated['ordem'] ?? 0,
                'disponivel' => $request->has('ativo') ? true : false,
                'padrao' => $request->has('padrao') ? true : false,
            ]);

            return redirect()->route('comerciantes.produtos.configuracoes.show', $configuracao)
                ->with('success', 'Item adicionado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao adicionar item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Atualizar item da configuração
     */
    public function updateItem(Request $request, ProdutoConfiguracao $configuracao, $itemId)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            return redirect()->back()->with('error', 'Não autorizado');
        }

        $item = $configuracao->itens()->findOrFail($itemId);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'valor_adicional' => 'nullable|numeric|min:0',
            'ordem' => 'nullable|integer|min:0',
            'disponivel' => 'boolean',
            'padrao' => 'boolean'
        ]);

        try {
            $item->update([
                'nome' => $validated['nome'],
                'descricao' => $validated['descricao'] ?? null,
                'valor_adicional' => $validated['valor_adicional'] ?? 0.00,
                'ordem' => $validated['ordem'] ?? 0,
                'disponivel' => $request->has('ativo') ? true : false,
                'padrao' => $request->has('padrao') ? true : false,
            ]);

            return redirect()->route('comerciantes.produtos.configuracoes.show', $configuracao)
                ->with('success', 'Item atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar item: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Excluir item da configuração
     */
    public function destroyItem(ProdutoConfiguracao $configuracao, $itemId)
    {
        // Verificar se a configuração pertence à empresa
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($configuracao->empresa_id !== $empresaId) {
            return redirect()->back()->with('error', 'Não autorizado');
        }

        try {
            $item = $configuracao->itens()->findOrFail($itemId);
            $item->delete();

            return redirect()->route('comerciantes.produtos.configuracoes.show', $configuracao)
                ->with('success', 'Item excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir item: ' . $e->getMessage());
        }
    }
}
