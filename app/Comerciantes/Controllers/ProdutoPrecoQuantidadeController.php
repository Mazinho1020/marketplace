<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoPrecoQuantidade;
use App\Models\ProdutoVariacaoCombinacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutoPrecoQuantidadeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $empresaId = session('empresa_id', 1);
            $produtoId = $request->get('produto_id');

            // Se for uma requisição AJAX, retornar apenas as variações
            if ($request->get('ajax') == 1) {
                if (!$produtoId) {
                    return response()->json(['variacoes' => []]);
                }

                $variacoes = ProdutoVariacaoCombinacao::where('produto_id', $produtoId)
                    ->where('ativo', true)
                    ->orderBy('nome')
                    ->get(['id', 'nome', 'preco_adicional']);

                return response()->json(['variacoes' => $variacoes]);
            }

            // Lógica normal para views
            if ($produtoId) {
                $produto = Produto::where('empresa_id', $empresaId)->findOrFail($produtoId);
                $precosQuantidade = ProdutoPrecoQuantidade::where('produto_id', $produtoId)
                    ->where('empresa_id', $empresaId)
                    ->with(['variacao', 'produto'])
                    ->orderBy('quantidade_minima')
                    ->paginate(20);
            } else {
                $produto = null;
                $precosQuantidade = ProdutoPrecoQuantidade::where('empresa_id', $empresaId)
                    ->with(['variacao', 'produto'])
                    ->orderBy('produto_id')
                    ->orderBy('quantidade_minima')
                    ->paginate(20);
            }

            $variacoes = $produtoId ?
                ProdutoVariacaoCombinacao::where('produto_id', $produtoId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get() :
                collect();

            return view('comerciantes.produtos.precos-quantidade.index', compact('produto', 'precosQuantidade', 'variacoes'));
        } catch (\Exception $e) {
            if ($request->get('ajax') == 1) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ], 500);
            }
            throw $e;
        }
    }

    public function create(Request $request)
    {
        $empresaId = session('empresa_id', 1);
        $produtoId = $request->get('produto_id');
        $produto = $produtoId ? Produto::where('empresa_id', $empresaId)->findOrFail($produtoId) : null;

        $variacoes = $produtoId ?
            ProdutoVariacaoCombinacao::where('produto_id', $produtoId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get() :
            collect();

        $produtos = Produto::where('ativo', true)
            ->where('empresa_id', $empresaId)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.precos-quantidade.create', compact('produto', 'variacoes', 'produtos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produto_id' => 'required|exists:produtos,id',
            'variacao_id' => 'nullable|exists:produto_variacoes_combinacoes,id',
            'quantidade_minima' => 'required|numeric|min:1',
            'quantidade_maxima' => 'nullable|numeric|gt:quantidade_minima',
            'preco' => 'required|string', // String porque vem em formato brasileiro
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'ativo' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $empresaId = session('empresa_id', 1);

            // Converter preço do formato brasileiro
            $preco = str_replace(['.', ','], ['', '.'], $request->preco);

            // Verificar sobreposição de faixas
            $this->verificarSobreposicao(
                $request->produto_id,
                $request->variacao_id,
                $request->quantidade_minima,
                $request->quantidade_maxima
            );

            $precoPorQuantidade = ProdutoPrecoQuantidade::create([
                'empresa_id' => $empresaId,
                'produto_id' => $request->produto_id,
                'variacao_id' => $request->variacao_id ?: null,
                'quantidade_minima' => $request->quantidade_minima,
                'quantidade_maxima' => $request->quantidade_maxima,
                'preco' => $preco,
                'desconto_percentual' => $request->desconto_percentual ?? 0,
                'ativo' => $request->has('ativo'),
                'sync_status' => 'pendente'
            ]);

            DB::commit();

            return redirect()->route('comerciantes.produtos.precos-quantidade.index', ['produto_id' => $request->produto_id])
                ->with('success', 'Preço por quantidade criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao criar preço por quantidade: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $empresaId = session('empresa_id', 1);
        $precoQuantidade = ProdutoPrecoQuantidade::where('empresa_id', $empresaId)
            ->with(['variacao', 'produto'])
            ->findOrFail($id);

        $produto = $precoQuantidade->produto;

        return view('comerciantes.produtos.precos-quantidade.show', compact('produto', 'precoQuantidade'));
    }

    public function edit($id)
    {
        $empresaId = session('empresa_id', 1);
        $precoQuantidade = ProdutoPrecoQuantidade::where('empresa_id', $empresaId)
            ->with(['produto'])
            ->findOrFail($id);

        $produto = $precoQuantidade->produto;

        $variacoes = ProdutoVariacaoCombinacao::where('produto_id', $produto->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.precos-quantidade.edit', compact('produto', 'precoQuantidade', 'variacoes'));
    }

    public function update(Request $request, $id)
    {
        $empresaId = session('empresa_id', 1);
        $precoQuantidade = ProdutoPrecoQuantidade::where('empresa_id', $empresaId)
            ->with(['produto'])
            ->findOrFail($id);

        $produto = $precoQuantidade->produto;

        $validator = Validator::make($request->all(), [
            'variacao_id' => 'nullable|exists:produto_variacoes_combinacoes,id',
            'quantidade_minima' => 'required|numeric|min:1',
            'quantidade_maxima' => 'nullable|numeric|gt:quantidade_minima',
            'preco' => 'required|string',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'ativo' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Converter preço do formato brasileiro
            $preco = str_replace(['.', ','], ['', '.'], $request->preco);

            // Verificar sobreposição de faixas (excluindo o registro atual)
            $this->verificarSobreposicao(
                $produto->id,
                $request->variacao_id,
                $request->quantidade_minima,
                $request->quantidade_maxima,
                $id
            );

            $precoQuantidade->update([
                'variacao_id' => $request->variacao_id,
                'quantidade_minima' => $request->quantidade_minima,
                'quantidade_maxima' => $request->quantidade_maxima,
                'preco' => $preco,
                'desconto_percentual' => $request->desconto_percentual ?? 0,
                'ativo' => $request->boolean('ativo'),
                'sync_status' => 'pendente'
            ]);

            DB::commit();

            return redirect()->route('comerciantes.produtos.precos-quantidade.index', ['produto_id' => $produto->id])
                ->with('success', 'Preço por quantidade atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao atualizar preço por quantidade: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $empresaId = session('empresa_id', 1);
        $precoQuantidade = ProdutoPrecoQuantidade::where('empresa_id', $empresaId)
            ->with(['produto'])
            ->findOrFail($id);

        $produto = $precoQuantidade->produto;

        try {
            $precoQuantidade->delete();

            return redirect()->route('comerciantes.produtos.precos-quantidade.index', ['produto_id' => $produto->id])
                ->with('success', 'Preço por quantidade removido com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao remover preço por quantidade: ' . $e->getMessage());
        }
    }

    public function calcularPreco(Request $request, $produtoId)
    {
        $validator = Validator::make($request->all(), [
            'quantidade' => 'required|numeric|min:1',
            'variacao_id' => 'nullable|exists:produto_variacoes_combinacoes,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        }

        $produto = Produto::findOrFail($produtoId);
        $quantidade = $request->quantidade;
        $variacaoId = $request->variacao_id;

        // Buscar preço aplicável
        $precoAplicavel = ProdutoPrecoQuantidade::where('produto_id', $produtoId)
            ->where('ativo', true)
            ->where('quantidade_minima', '<=', $quantidade)
            ->where(function ($query) use ($quantidade) {
                $query->whereNull('quantidade_maxima')
                    ->orWhere('quantidade_maxima', '>=', $quantidade);
            })
            ->when($variacaoId, function ($query) use ($variacaoId) {
                $query->where('variacao_id', $variacaoId);
            })
            ->orderBy('quantidade_minima', 'desc')
            ->first();

        if ($precoAplicavel) {
            return response()->json([
                'preco_original' => $precoAplicavel->preco,
                'preco_com_desconto' => $precoAplicavel->preco_com_desconto,
                'desconto_percentual' => $precoAplicavel->desconto_percentual,
                'economia' => $precoAplicavel->economia,
                'faixa' => [
                    'minima' => $precoAplicavel->quantidade_minima,
                    'maxima' => $precoAplicavel->quantidade_maxima
                ]
            ]);
        }

        // Usar preço padrão do produto
        $precoBase = $variacaoId ?
            $produto->variacoes->find($variacaoId)->preco_adicional ?? $produto->preco_venda :
            $produto->preco_venda;

        return response()->json([
            'preco_original' => $precoBase,
            'preco_com_desconto' => $precoBase,
            'desconto_percentual' => 0,
            'economia' => 0,
            'faixa' => null
        ]);
    }

    public function porProduto($produto)
    {
        $empresaId = session('empresa_id', 1);
        $produto = Produto::where('empresa_id', $empresaId)->findOrFail($produto);

        $precosQuantidade = ProdutoPrecoQuantidade::where('produto_id', $produto->id)
            ->where('empresa_id', $empresaId)
            ->with(['variacao', 'produto'])
            ->orderBy('quantidade_minima')
            ->paginate(20);

        $variacoes = ProdutoVariacaoCombinacao::where('produto_id', $produto->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('comerciantes.produtos.precos-quantidade.produto', compact('produto', 'precosQuantidade', 'variacoes'));
    }

    private function verificarSobreposicao($produtoId, $variacaoId, $minima, $maxima, $excludeId = null)
    {
        $empresaId = session('empresa_id', 1);
        $query = ProdutoPrecoQuantidade::where('produto_id', $produtoId)
            ->where('empresa_id', $empresaId)
            ->where('ativo', true);

        if ($variacaoId) {
            $query->where('variacao_id', $variacaoId);
        } else {
            $query->whereNull('variacao_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existentes = $query->get();

        foreach ($existentes as $existente) {
            $existenteMax = $existente->quantidade_maxima ?? PHP_INT_MAX;
            $novaMax = $maxima ?? PHP_INT_MAX;

            // Verifica sobreposição
            if (!(
                $novaMax < $existente->quantidade_minima ||
                $minima > $existenteMax
            )) {
                throw new \Exception(
                    'Faixa de quantidade se sobrepõe com regra existente (' .
                        $existente->quantidade_minima . ' - ' .
                        ($existente->quantidade_maxima ?? '∞') . ')'
                );
            }
        }
    }
}
