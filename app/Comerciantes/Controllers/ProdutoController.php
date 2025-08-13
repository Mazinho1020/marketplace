<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoCategoria;
use App\Models\ProdutoMarca;
use App\Models\ProdutoSubcategoria;
use App\Services\EstoqueBaixoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = session('empresa_id', 1);

        $query = Produto::with(['categoria', 'marca', 'imagemPrincipal'])
            ->porEmpresa($empresaId)
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('busca')) {
            $busca = $request->get('busca');
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('sku', 'like', "%{$busca}%")
                    ->orWhere('codigo_barras', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->get('categoria_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('estoque_baixo')) {
            $query->where('controla_estoque', true)
                ->whereColumn('estoque_atual', '<=', 'estoque_minimo');
        }

        $produtos = $query->paginate(20);
        $categorias = ProdutoCategoria::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();

        return view('comerciantes.produtos.index', compact('produtos', 'categorias'));
    }

    public function create()
    {
        $empresaId = session('empresa_id', 1);

        $categorias = ProdutoCategoria::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();
        $marcas = ProdutoMarca::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();
        $subcategorias = ProdutoSubcategoria::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();

        return view('comerciantes.produtos.create', compact('categorias', 'marcas', 'subcategorias'));
    }

    public function store(Request $request)
    {
        $empresaId = session('empresa_id', 1);

        // Converter valores monetários do formato brasileiro para americano
        if ($request->has('preco_venda')) {
            $precoVenda = str_replace(['.', ','], ['', '.'], $request->preco_venda);
            $request->merge(['preco_venda' => $precoVenda]);
        }

        if ($request->has('preco_compra')) {
            $precoCompra = str_replace(['.', ','], ['', '.'], $request->preco_compra);
            $request->merge(['preco_compra' => $precoCompra]);
        }

        if ($request->has('preco_promocional')) {
            $precoPromocional = str_replace(['.', ','], ['', '.'], $request->preco_promocional);
            $request->merge(['preco_promocional' => $precoPromocional]);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'required|exists:produto_categorias,id',
            'preco_venda' => 'required|numeric|min:0',
            'tipo' => 'required|in:produto,insumo,complemento,servico,combo,kit',
            'imagem_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $dadosProduto = $request->all();
            $dadosProduto['empresa_id'] = $empresaId;
            $dadosProduto['slug'] = Str::slug($request->nome);
            $dadosProduto['ativo'] = $request->has('ativo');
            $dadosProduto['controla_estoque'] = $request->has('controla_estoque');
            $dadosProduto['destaque'] = $request->has('destaque');

            // Gerar SKU automaticamente se não fornecido
            if (empty($dadosProduto['sku'])) {
                $dadosProduto['sku'] = $this->gerarSku($request->nome, $empresaId);
            }

            $produto = Produto::create($dadosProduto);

            // Upload da imagem principal
            if ($request->hasFile('imagem_principal')) {
                $this->processarImagemPrincipal($produto, $request->file('imagem_principal'));
            }

            // Criar notificação de estoque baixo se necessário
            if ($produto->estoque_baixo) {
                $this->criarNotificacaoEstoqueBaixo($produto);
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.show', $produto)
                ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withErrors(['erro' => 'Erro ao criar produto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Produto $produto)
    {
        $this->verificarEmpresaProduto($produto);

        $produto->load([
            'categoria',
            'subcategoria',
            'marca',
            'imagens',
            'configuracoes.itens',
            'variacoes',
            'movimentacoes' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        // Estatísticas básicas
        $estatisticas = [
            'vendas_mes' => $produto->movimentacoes()
                ->where('tipo', 'venda')
                ->whereMonth('created_at', now()->month)
                ->sum('quantidade'),
            'faturamento_mes' => $produto->movimentacoes()
                ->where('tipo', 'venda')
                ->whereMonth('created_at', now()->month)
                ->sum('valor_total'),
            'estoque_atual' => $produto->estoque_atual,
            'ultima_venda' => $produto->movimentacoes()
                ->where('tipo', 'venda')
                ->latest()
                ->first()
        ];

        return view('comerciantes.produtos.show', compact('produto', 'estatisticas'));
    }

    public function edit(Produto $produto)
    {
        $this->verificarEmpresaProduto($produto);

        $empresaId = session('empresa_id', 1);

        $categorias = ProdutoCategoria::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();
        $marcas = ProdutoMarca::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();
        $subcategorias = ProdutoSubcategoria::porEmpresa($empresaId)->ativas()->orderBy('nome')->get();

        return view('comerciantes.produtos.edit', compact('produto', 'categorias', 'marcas', 'subcategorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $this->verificarEmpresaProduto($produto);

        // Converter valores monetários do formato brasileiro para americano
        if ($request->has('preco_venda')) {
            $precoVenda = str_replace(['.', ','], ['', '.'], $request->preco_venda);
            $request->merge(['preco_venda' => $precoVenda]);
        }

        if ($request->has('preco_compra')) {
            $precoCompra = str_replace(['.', ','], ['', '.'], $request->preco_compra);
            $request->merge(['preco_compra' => $precoCompra]);
        }

        if ($request->has('preco_promocional')) {
            $precoPromocional = str_replace(['.', ','], ['', '.'], $request->preco_promocional);
            $request->merge(['preco_promocional' => $precoPromocional]);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'categoria_id' => 'required|exists:produto_categorias,id',
            'preco_venda' => 'required|numeric|min:0',
            'tipo' => 'required|in:produto,insumo,complemento,servico,combo,kit',
            'imagem_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $dadosAnteriores = $produto->toArray();

            $dadosProduto = $request->all();
            $dadosProduto['ativo'] = $request->has('ativo');
            $dadosProduto['controla_estoque'] = $request->has('controla_estoque');
            $dadosProduto['destaque'] = $request->has('destaque');

            // Atualizar slug se o nome mudou
            if ($produto->nome !== $request->nome) {
                $dadosProduto['slug'] = Str::slug($request->nome);
            }

            $produto->update($dadosProduto);

            // Registrar histórico de preços se mudou
            if ($dadosAnteriores['preco_venda'] != $produto->preco_venda) {
                $this->registrarHistoricoPreco($produto, $dadosAnteriores);
            }

            // Upload da nova imagem principal
            if ($request->hasFile('imagem_principal')) {
                $this->processarImagemPrincipal($produto, $request->file('imagem_principal'));
            }

            DB::commit();

            return redirect()
                ->route('comerciantes.produtos.show', $produto)
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()
                ->withErrors(['erro' => 'Erro ao atualizar produto: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Produto $produto)
    {
        $this->verificarEmpresaProduto($produto);

        try {
            $produto->delete();

            return redirect()
                ->route('comerciantes.produtos.index')
                ->with('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['erro' => 'Erro ao excluir produto: ' . $e->getMessage()]);
        }
    }

    public function movimentacao(Request $request, Produto $produto)
    {
        $this->verificarEmpresaProduto($produto);

        $request->validate([
            'tipo' => 'required|in:entrada,saida,ajuste',
            'quantidade' => 'required|numeric|min:0.001',
            'motivo' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:500'
        ]);

        try {
            $estoqueAnterior = $produto->estoque_atual;

            if ($request->tipo === 'entrada') {
                $produto->adicionarEstoque(
                    $request->quantidade,
                    $request->motivo,
                    $request->observacoes
                );
            } elseif ($request->tipo === 'saida') {
                if (!$produto->baixarEstoque(
                    $request->quantidade,
                    $request->motivo,
                    $request->observacoes
                )) {
                    return back()->withErrors(['erro' => 'Estoque insuficiente para esta operação.']);
                }
            } else { // ajuste
                $novoEstoque = $request->quantidade;
                $diferenca = $novoEstoque - $estoqueAnterior;

                $produto->update(['estoque_atual' => $novoEstoque]);

                $produto->movimentacoes()->create([
                    'empresa_id' => $produto->empresa_id,
                    'tipo' => 'ajuste',
                    'quantidade' => abs($diferenca),
                    'valor_unitario' => $produto->preco_compra ?? 0,
                    'valor_total' => abs($diferenca) * ($produto->preco_compra ?? 0),
                    'estoque_anterior' => $estoqueAnterior,
                    'estoque_posterior' => $novoEstoque,
                    'motivo' => $request->motivo,
                    'observacoes' => $request->observacoes,
                    'data_movimento' => now(),
                    'sync_status' => 'pendente'
                ]);
            }

            // Verificar se estoque ficou baixo e criar notificação
            if ($produto->estoque_baixo) {
                $this->criarNotificacaoEstoqueBaixo($produto);
            }

            return back()->with('success', 'Movimentação registrada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['erro' => 'Erro ao registrar movimentação: ' . $e->getMessage()]);
        }
    }

    public function duplicate(Produto $produto)
    {
        $this->verificarEmpresaProduto($produto);

        try {
            $novoProduto = $produto->replicate();
            $novoProduto->nome = $produto->nome . ' (Cópia)';
            $novoProduto->slug = Str::slug($novoProduto->nome);
            $novoProduto->sku = $this->gerarSku($novoProduto->nome, $produto->empresa_id);
            $novoProduto->codigo_barras = null;
            $novoProduto->estoque_atual = 0;
            $novoProduto->save();

            return redirect()
                ->route('comerciantes.produtos.edit', $novoProduto)
                ->with('success', 'Produto duplicado com sucesso! Edite as informações necessárias.');
        } catch (\Exception $e) {
            return back()->withErrors(['erro' => 'Erro ao duplicar produto: ' . $e->getMessage()]);
        }
    }

    // Métodos auxiliares
    private function verificarEmpresaProduto(Produto $produto)
    {
        $empresaId = session('empresa_id', 1);

        if ($produto->empresa_id !== $empresaId) {
            abort(403, 'Produto não pertence à sua empresa.');
        }
    }

    private function gerarSku($nome, $empresaId)
    {
        $prefixo = strtoupper(substr(Str::slug($nome), 0, 3));
        $numero = 1;

        do {
            $sku = $prefixo . str_pad($numero, 4, '0', STR_PAD_LEFT);
            $existe = Produto::where('empresa_id', $empresaId)
                ->where('sku', $sku)
                ->exists();
            $numero++;
        } while ($existe);

        return $sku;
    }

    private function processarImagemPrincipal(Produto $produto, $arquivo)
    {
        $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
        $caminho = $arquivo->storeAs('produtos', $nomeArquivo, 'public');

        // Remover imagem principal anterior se existir
        $imagemAnterior = $produto->imagemPrincipal;
        if ($imagemAnterior) {
            Storage::disk('public')->delete('produtos/' . $imagemAnterior->arquivo);
            $imagemAnterior->delete();
        }

        // Criar nova imagem principal
        $produto->imagens()->create([
            'empresa_id' => $produto->empresa_id,
            'tipo' => 'principal',
            'arquivo' => $nomeArquivo,
            'titulo' => $produto->nome,
            'alt_text' => $produto->nome,
            'ordem' => 1,
            'tamanho_arquivo' => $arquivo->getSize(),
            'ativo' => true,
            'sync_status' => 'pendente'
        ]);
    }

    private function registrarHistoricoPreco(Produto $produto, $dadosAnteriores)
    {
        $produto->historicoPrecos()->create([
            'empresa_id' => $produto->empresa_id,
            'preco_compra_anterior' => $dadosAnteriores['preco_compra'],
            'preco_compra_novo' => $produto->preco_compra,
            'preco_venda_anterior' => $dadosAnteriores['preco_venda'],
            'preco_venda_novo' => $produto->preco_venda,
            'margem_anterior' => $dadosAnteriores['margem_lucro'],
            'margem_nova' => $produto->margem_lucro,
            'motivo' => 'Atualização manual',
            'usuario_id' => session('usuario_id', 1),
            'data_alteracao' => now(),
            'sync_status' => 'pendente'
        ]);
    }

    private function criarNotificacaoEstoqueBaixo(Produto $produto)
    {
        $estoqueBaixoService = new EstoqueBaixoService();

        try {
            // Verificar se produto tem estoque baixo ou zerado
            if ($produto->controla_estoque && $produto->ativo) {
                if ($produto->quantidade_estoque <= 0) {
                    $estoqueBaixoService->criarNotificacaoEstoqueZerado($produto);
                } elseif ($produto->estoque_minimo > 0 && $produto->quantidade_estoque <= $produto->estoque_minimo) {
                    $estoqueBaixoService->criarNotificacaoEstoqueBaixo($produto);
                }
            }
        } catch (\Exception $e) {
            logger('Erro ao criar notificação de estoque: ' . $e->getMessage());
        }
    }

    /**
     * Exibe relatório de estoque baixo
     */
    public function relatorioEstoque(Request $request)
    {
        $empresaId = session('empresa_id', 1);
        $estoqueBaixoService = new EstoqueBaixoService();

        $relatorio = $estoqueBaixoService->relatorioProblemasEstoque($empresaId);

        return view('comerciantes.produtos.relatorio-estoque', compact('relatorio'));
    }

    /**
     * Verifica produtos com estoque baixo via AJAX
     */
    public function verificarEstoqueBaixo(Request $request)
    {
        $empresaId = session('empresa_id', 1);
        $estoqueBaixoService = new EstoqueBaixoService();

        $resultados = $estoqueBaixoService->executarVerificacaoCompleta($empresaId);

        return response()->json([
            'success' => true,
            'dados' => $resultados,
            'message' => "Verificação concluída. {$resultados['total_notificacoes_criadas']} notificações criadas."
        ]);
    }

    /**
     * Atualiza estoque de um produto
     */
    public function atualizarEstoque(Request $request, Produto $produto)
    {
        $request->validate([
            'tipo_movimentacao' => 'required|in:entrada,saida,ajuste',
            'quantidade' => 'required|numeric|min:0',
            'observacao' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $tipoMovimentacao = $request->tipo_movimentacao;
            $quantidade = $request->quantidade;
            $observacao = $request->observacao;

            // Calcular nova quantidade
            $quantidadeAnterior = $produto->quantidade_estoque;

            switch ($tipoMovimentacao) {
                case 'entrada':
                    $novaQuantidade = $quantidadeAnterior + $quantidade;
                    break;
                case 'saida':
                    $novaQuantidade = max(0, $quantidadeAnterior - $quantidade);
                    break;
                case 'ajuste':
                    $novaQuantidade = $quantidade;
                    break;
            }

            // Atualizar produto
            $produto->update(['quantidade_estoque' => $novaQuantidade]);

            // Criar movimentação
            $produto->movimentacoes()->create([
                'tipo' => $tipoMovimentacao,
                'quantidade_anterior' => $quantidadeAnterior,
                'quantidade_movimentada' => $tipoMovimentacao === 'ajuste' ? ($novaQuantidade - $quantidadeAnterior) : $quantidade,
                'quantidade_atual' => $novaQuantidade,
                'observacao' => $observacao,
                'usuario_id' => session('usuario_id', 1),
                'data_movimentacao' => now()
            ]);

            // Verificar se precisa criar notificação de estoque baixo
            $this->criarNotificacaoEstoqueBaixo($produto);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estoque atualizado com sucesso!',
                'quantidade_atual' => $novaQuantidade
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar estoque: ' . $e->getMessage()
            ], 500);
        }
    }
}
