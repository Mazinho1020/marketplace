<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoRelacionado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProdutoRelacionadoController extends Controller
{
    /**
     * Listar produtos relacionados
     */
    public function index(Request $request, $produtoId)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $produto = Produto::where('id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        $relacionados = ProdutoRelacionado::with(['produtoRelacionado' => function ($q) {
            $q->select('id', 'nome', 'sku', 'preco_venda', 'estoque_atual', 'controla_estoque');
        }])
            ->where('produto_id', $produtoId)
            ->where('empresa_id', $empresaId);

        // Filtros
        if ($request->filled('tipo_relacao')) {
            $relacionados->where('tipo_relacao', $request->tipo_relacao);
        }

        if ($request->filled('ativo')) {
            $relacionados->where('ativo', $request->ativo);
        }

        $relacionados = $relacionados->orderBy('tipo_relacao')
            ->orderBy('ordem')
            ->get();

        // Lista de produtos para adicionar relacionamento
        $produtosDisponiveis = Produto::where('empresa_id', $empresaId)
            ->where('id', '!=', $produtoId)
            ->where('ativo', true)
            ->whereNotIn('id', function ($query) use ($produtoId, $empresaId) {
                $query->select('produto_relacionado_id')
                    ->from('produto_relacionados')
                    ->where('produto_id', $produtoId)
                    ->where('empresa_id', $empresaId);
            })
            ->orderBy('nome')
            ->get(['id', 'nome', 'sku', 'preco_venda']);

        return view('comerciantes.produtos.relacionados.index', compact(
            'produto',
            'relacionados',
            'produtosDisponiveis'
        ));
    }

    /**
     * Adicionar produto relacionado
     */
    public function store(Request $request, $produtoId)
    {
        $request->validate([
            'produto_relacionado_id' => 'required|exists:produtos,id',
            'tipo_relacao' => 'required|in:similar,complementar,acessorio,substituto,kit,cross-sell,up-sell',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        // Verificar se o produto principal existe
        $produto = Produto::where('id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        // Verificar se o produto relacionado existe e pertence à mesma empresa
        $produtoRelacionado = Produto::where('id', $request->produto_relacionado_id)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        // Verificar se já existe relacionamento
        $existeRelacionamento = ProdutoRelacionado::where('produto_id', $produtoId)
            ->where('produto_relacionado_id', $request->produto_relacionado_id)
            ->where('tipo_relacao', $request->tipo_relacao)
            ->where('empresa_id', $empresaId)
            ->exists();

        if ($existeRelacionamento) {
            return redirect()->back()->withErrors(['error' => 'Este relacionamento já existe!']);
        }

        try {
            DB::beginTransaction();

            ProdutoRelacionado::create([
                'empresa_id' => $empresaId,
                'produto_id' => $produtoId,
                'produto_relacionado_id' => $request->produto_relacionado_id,
                'tipo_relacao' => $request->tipo_relacao,
                'ordem' => $request->ordem ?? 0,
                'ativo' => $request->ativo ?? true
            ]);

            // Se for um relacionamento bidirecional, criar o relacionamento reverso
            if (in_array($request->tipo_relacao, ['similar', 'complementar'])) {
                ProdutoRelacionado::create([
                    'empresa_id' => $empresaId,
                    'produto_id' => $request->produto_relacionado_id,
                    'produto_relacionado_id' => $produtoId,
                    'tipo_relacao' => $request->tipo_relacao,
                    'ordem' => $request->ordem ?? 0,
                    'ativo' => $request->ativo ?? true
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Produto relacionado adicionado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erro ao adicionar produto relacionado: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualizar produto relacionado
     */
    public function update(Request $request, $produtoId, $relacionadoId)
    {
        $request->validate([
            'tipo_relacao' => 'required|in:similar,complementar,acessorio,substituto,kit,cross-sell,up-sell',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'boolean'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $relacionado = ProdutoRelacionado::where('id', $relacionadoId)
            ->where('produto_id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        try {
            $relacionado->update([
                'tipo_relacao' => $request->tipo_relacao,
                'ordem' => $request->ordem ?? $relacionado->ordem,
                'ativo' => $request->ativo ?? $relacionado->ativo
            ]);

            return redirect()->back()->with('success', 'Produto relacionado atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar produto relacionado: ' . $e->getMessage()]);
        }
    }

    /**
     * Remover produto relacionado
     */
    public function destroy($produtoId, $relacionadoId)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        $relacionado = ProdutoRelacionado::where('id', $relacionadoId)
            ->where('produto_id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Remover relacionamento reverso se existir
            if (in_array($relacionado->tipo_relacao, ['similar', 'complementar'])) {
                ProdutoRelacionado::where('produto_id', $relacionado->produto_relacionado_id)
                    ->where('produto_relacionado_id', $relacionado->produto_id)
                    ->where('tipo_relacao', $relacionado->tipo_relacao)
                    ->where('empresa_id', $empresaId)
                    ->delete();
            }

            $relacionado->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Produto relacionado removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Erro ao remover produto relacionado: ' . $e->getMessage()]);
        }
    }

    /**
     * Atualizar ordem em lote
     */
    public function updateOrdem(Request $request, $produtoId)
    {
        $request->validate([
            'relacionados' => 'required|array',
            'relacionados.*.id' => 'required|exists:produto_relacionados,id',
            'relacionados.*.ordem' => 'required|integer|min:0'
        ]);

        $empresaId = Auth::guard('comerciante')->user()->empresa_id;

        try {
            DB::beginTransaction();

            foreach ($request->relacionados as $data) {
                ProdutoRelacionado::where('id', $data['id'])
                    ->where('produto_id', $produtoId)
                    ->where('empresa_id', $empresaId)
                    ->update(['ordem' => $data['ordem']]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ordem atualizada com sucesso!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Erro ao atualizar ordem: ' . $e->getMessage()]);
        }
    }

    /**
     * Buscar produtos para relacionamento via AJAX
     */
    public function buscarProdutos(Request $request, $produtoId)
    {
        $empresaId = Auth::guard('comerciante')->user()->empresa_id;
        $termo = $request->get('q', '');

        $produtos = Produto::where('empresa_id', $empresaId)
            ->where('id', '!=', $produtoId)
            ->where('ativo', true)
            ->where(function ($query) use ($termo) {
                $query->where('nome', 'LIKE', "%{$termo}%")
                    ->orWhere('sku', 'LIKE', "%{$termo}%");
            })
            ->whereNotIn('id', function ($query) use ($produtoId, $empresaId) {
                $query->select('produto_relacionado_id')
                    ->from('produto_relacionados')
                    ->where('produto_id', $produtoId)
                    ->where('empresa_id', $empresaId);
            })
            ->limit(20)
            ->get(['id', 'nome', 'sku', 'preco_venda']);

        return response()->json([
            'results' => $produtos->map(function ($produto) {
                return [
                    'id' => $produto->id,
                    'text' => "{$produto->nome} ({$produto->sku}) - R$ " . number_format($produto->preco_venda, 2, ',', '.')
                ];
            })
        ]);
    }
}
