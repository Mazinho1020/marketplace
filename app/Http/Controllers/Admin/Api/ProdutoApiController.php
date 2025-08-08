<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Models\ProdutoCategoria;
use App\Models\ProdutoMarca;
use App\Models\ProdutoMovimentacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProdutoApiController extends Controller
{
    /**
     * Dashboard de produtos - estatísticas gerais
     */
    public function dashboard(Request $request)
    {
        try {
            $empresaId = $request->get('empresa_id');

            // Estatísticas básicas
            $stats = [
                'total_produtos' => Produto::porEmpresa($empresaId)->count(),
                'produtos_ativos' => Produto::porEmpresa($empresaId)->ativos()->count(),
                'produtos_destaque' => Produto::porEmpresa($empresaId)->destaque()->count(),
                'estoque_baixo' => Produto::porEmpresa($empresaId)
                    ->where('controla_estoque', true)
                    ->whereColumn('estoque_atual', '<=', 'estoque_minimo')
                    ->count(),
                'sem_estoque' => Produto::porEmpresa($empresaId)
                    ->where('controla_estoque', true)
                    ->where('estoque_atual', '<=', 0)
                    ->count(),
                'valor_total_estoque' => Produto::porEmpresa($empresaId)
                    ->selectRaw('SUM(estoque_atual * preco_compra) as total')
                    ->value('total') ?: 0
            ];

            // Produtos mais vendidos (últimos 30 dias)
            $maisVendidos = Produto::select([
                'produtos.id',
                'produtos.nome',
                'produtos.preco_venda',
                DB::raw('SUM(produto_movimentacoes.quantidade) as total_vendido'),
                DB::raw('SUM(produto_movimentacoes.valor_total) as faturamento')
            ])
                ->join('produto_movimentacoes', 'produtos.id', '=', 'produto_movimentacoes.produto_id')
                ->where('produtos.empresa_id', $empresaId)
                ->where('produto_movimentacoes.tipo', 'venda')
                ->where('produto_movimentacoes.created_at', '>=', now()->subDays(30))
                ->groupBy('produtos.id', 'produtos.nome', 'produtos.preco_venda')
                ->orderBy('total_vendido', 'desc')
                ->limit(10)
                ->get();

            // Movimentações recentes
            $movimentacoesRecentes = ProdutoMovimentacao::with(['produto'])
                ->where('empresa_id', $empresaId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Categorias com mais produtos
            $categorias = ProdutoCategoria::select([
                'produto_categorias.id',
                'produto_categorias.nome',
                DB::raw('COUNT(produtos.id) as total_produtos')
            ])
                ->leftJoin('produtos', 'produto_categorias.id', '=', 'produtos.categoria_id')
                ->where('produto_categorias.empresa_id', $empresaId)
                ->groupBy('produto_categorias.id', 'produto_categorias.nome')
                ->orderBy('total_produtos', 'desc')
                ->get();

            // Alertas de estoque
            $alertasEstoque = Produto::with(['categoria'])
                ->porEmpresa($empresaId)
                ->where('controla_estoque', true)
                ->whereColumn('estoque_atual', '<=', 'estoque_minimo')
                ->orderBy('estoque_atual')
                ->limit(20)
                ->get()
                ->map(function ($produto) {
                    return [
                        'id' => $produto->id,
                        'nome' => $produto->nome,
                        'categoria' => $produto->categoria->nome ?? 'Sem categoria',
                        'estoque_atual' => $produto->estoque_atual,
                        'estoque_minimo' => $produto->estoque_minimo,
                        'percentual' => $produto->estoque_minimo > 0
                            ? round(($produto->estoque_atual / $produto->estoque_minimo) * 100, 1)
                            : 0
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'estatisticas' => $stats,
                    'mais_vendidos' => $maisVendidos,
                    'movimentacoes_recentes' => $movimentacoesRecentes,
                    'categorias' => $categorias,
                    'alertas_estoque' => $alertasEstoque
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar produtos com filtros
     */
    public function index(Request $request)
    {
        try {
            $empresaId = $request->get('empresa_id');
            $perPage = $request->get('per_page', 20);

            $query = Produto::with(['categoria', 'marca', 'imagemPrincipal'])
                ->porEmpresa($empresaId);

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

            if ($request->filled('marca_id')) {
                $query->where('marca_id', $request->get('marca_id'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->get('tipo'));
            }

            if ($request->has('ativo')) {
                $query->where('ativo', $request->boolean('ativo'));
            }

            if ($request->has('controla_estoque')) {
                $query->where('controla_estoque', $request->boolean('controla_estoque'));
            }

            if ($request->has('estoque_baixo')) {
                $query->where('controla_estoque', true)
                    ->whereColumn('estoque_atual', '<=', 'estoque_minimo');
            }

            if ($request->has('sem_estoque')) {
                $query->where('controla_estoque', true)
                    ->where('estoque_atual', '<=', 0);
            }

            // Ordenação
            $orderBy = $request->get('order_by', 'created_at');
            $orderDirection = $request->get('order_direction', 'desc');
            $query->orderBy($orderBy, $orderDirection);

            $produtos = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $produtos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar produtos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter detalhes de um produto
     */
    public function show(Request $request, $id)
    {
        try {
            $empresaId = $request->get('empresa_id');

            $produto = Produto::with([
                'categoria',
                'subcategoria',
                'marca',
                'imagens',
                'configuracoes.itens',
                'variacoes',
                'movimentacoes' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(20);
                },
                'historicoPrecos' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(10);
                },
                'fornecedores',
                'relacionados.produtoRelacionado'
            ])
                ->porEmpresa($empresaId)
                ->findOrFail($id);

            // Estatísticas do produto
            $estatisticas = [
                'vendas_total' => $produto->movimentacoes()
                    ->where('tipo', 'venda')
                    ->sum('quantidade'),
                'vendas_mes' => $produto->movimentacoes()
                    ->where('tipo', 'venda')
                    ->whereMonth('created_at', now()->month)
                    ->sum('quantidade'),
                'faturamento_total' => $produto->movimentacoes()
                    ->where('tipo', 'venda')
                    ->sum('valor_total'),
                'faturamento_mes' => $produto->movimentacoes()
                    ->where('tipo', 'venda')
                    ->whereMonth('created_at', now()->month)
                    ->sum('valor_total'),
                'ultima_venda' => $produto->movimentacoes()
                    ->where('tipo', 'venda')
                    ->latest('created_at')
                    ->first()?->created_at,
                'giro_estoque' => $this->calcularGiroEstoque($produto)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'produto' => $produto,
                    'estatisticas' => $estatisticas
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao carregar produto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Criar novo produto via API
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'empresa_id' => 'required|exists:empresas,id',
                'nome' => 'required|string|max:255',
                'categoria_id' => 'required|exists:produto_categorias,id',
                'preco_venda' => 'required|numeric|min:0',
                'tipo' => 'required|in:produto,insumo,complemento,servico,combo,kit'
            ]);

            DB::beginTransaction();

            $dadosProduto = $request->all();
            $dadosProduto['slug'] = Str::slug($request->nome);
            $dadosProduto['ativo'] = $request->boolean('ativo', true);
            $dadosProduto['controla_estoque'] = $request->boolean('controla_estoque', false);
            $dadosProduto['destaque'] = $request->boolean('destaque', false);

            // Gerar SKU automaticamente se não fornecido
            if (empty($dadosProduto['sku'])) {
                $dadosProduto['sku'] = $this->gerarSku($request->nome, $request->empresa_id);
            }

            $produto = Produto::create($dadosProduto);

            // Criar notificação de produto criado
            $this->criarNotificacaoProdutoCriado($produto);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $produto->load(['categoria', 'marca']),
                'message' => 'Produto criado com sucesso!'
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'error' => 'Erro ao criar produto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar produto via API
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                'categoria_id' => 'required|exists:produto_categorias,id',
                'preco_venda' => 'required|numeric|min:0',
                'tipo' => 'required|in:produto,insumo,complemento,servico,combo,kit'
            ]);

            $empresaId = $request->get('empresa_id');
            $produto = Produto::porEmpresa($empresaId)->findOrFail($id);

            DB::beginTransaction();

            $dadosAnteriores = $produto->toArray();

            $dadosProduto = $request->all();
            $dadosProduto['ativo'] = $request->boolean('ativo', $produto->ativo);
            $dadosProduto['controla_estoque'] = $request->boolean('controla_estoque', $produto->controla_estoque);
            $dadosProduto['destaque'] = $request->boolean('destaque', $produto->destaque);

            // Atualizar slug se o nome mudou
            if ($produto->nome !== $request->nome) {
                $dadosProduto['slug'] = Str::slug($request->nome);
            }

            $produto->update($dadosProduto);

            // Registrar histórico de preços se mudou
            if ($dadosAnteriores['preco_venda'] != $produto->preco_venda) {
                $this->registrarHistoricoPreco($produto, $dadosAnteriores);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $produto->load(['categoria', 'marca']),
                'message' => 'Produto atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar produto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar estoque de produto
     */
    public function atualizarEstoque(Request $request, $id)
    {
        try {
            $request->validate([
                'tipo' => 'required|in:entrada,saida,ajuste',
                'quantidade' => 'required|numeric|min:0.001',
                'motivo' => 'required|string|max:255',
                'observacoes' => 'nullable|string|max:500'
            ]);

            $empresaId = $request->get('empresa_id');
            $produto = Produto::porEmpresa($empresaId)->findOrFail($id);

            if (!$produto->controla_estoque) {
                return response()->json([
                    'success' => false,
                    'error' => 'Este produto não controla estoque'
                ], 400);
            }

            $estoqueAnterior = $produto->estoque_atual;

            if ($request->tipo === 'entrada') {
                $resultado = $produto->adicionarEstoque(
                    $request->quantidade,
                    $request->motivo,
                    $request->observacoes
                );
            } elseif ($request->tipo === 'saida') {
                $resultado = $produto->baixarEstoque(
                    $request->quantidade,
                    $request->motivo,
                    $request->observacoes
                );

                if (!$resultado) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Estoque insuficiente para esta operação'
                    ], 400);
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

            return response()->json([
                'success' => true,
                'data' => [
                    'produto' => $produto->fresh(),
                    'estoque_anterior' => $estoqueAnterior,
                    'estoque_atual' => $produto->estoque_atual
                ],
                'message' => 'Estoque atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao atualizar estoque: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Relatório de movimentações de estoque
     */
    public function relatorioMovimentacoes(Request $request)
    {
        try {
            $empresaId = $request->get('empresa_id');
            $dataInicio = $request->get('data_inicio', now()->startOfMonth());
            $dataFim = $request->get('data_fim', now()->endOfMonth());

            $movimentacoes = ProdutoMovimentacao::with(['produto'])
                ->where('empresa_id', $empresaId)
                ->whereBetween('data_movimento', [$dataInicio, $dataFim])
                ->when($request->filled('produto_id'), function ($query) use ($request) {
                    $query->where('produto_id', $request->get('produto_id'));
                })
                ->when($request->filled('tipo'), function ($query) use ($request) {
                    $query->where('tipo', $request->get('tipo'));
                })
                ->orderBy('data_movimento', 'desc')
                ->paginate($request->get('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $movimentacoes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ], 500);
        }
    }

    // Métodos auxiliares privados
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

    private function calcularGiroEstoque($produto)
    {
        if (!$produto->controla_estoque || $produto->estoque_atual <= 0) {
            return 0;
        }

        $vendasUltimos12Meses = $produto->movimentacoes()
            ->where('tipo', 'venda')
            ->where('created_at', '>=', now()->subMonths(12))
            ->sum('quantidade');

        return $produto->estoque_atual > 0 ? $vendasUltimos12Meses / $produto->estoque_atual : 0;
    }

    private function registrarHistoricoPreco($produto, $dadosAnteriores)
    {
        $produto->historicoPrecos()->create([
            'empresa_id' => $produto->empresa_id,
            'preco_compra_anterior' => $dadosAnteriores['preco_compra'],
            'preco_compra_novo' => $produto->preco_compra,
            'preco_venda_anterior' => $dadosAnteriores['preco_venda'],
            'preco_venda_novo' => $produto->preco_venda,
            'margem_anterior' => $dadosAnteriores['margem_lucro'],
            'margem_nova' => $produto->margem_lucro,
            'motivo' => 'Atualização via API',
            'usuario_id' => 1, // Sistema
            'data_alteracao' => now(),
            'sync_status' => 'pendente'
        ]);
    }

    private function criarNotificacaoEstoqueBaixo($produto)
    {
        // Integração com sistema de notificações existente
        try {
            // Aqui integraria com o sistema de notificações comerciantes
            logger('Notificação de estoque baixo via API para produto: ' . $produto->nome);
        } catch (\Exception $e) {
            logger('Erro ao criar notificação de estoque baixo via API: ' . $e->getMessage());
        }
    }

    private function criarNotificacaoProdutoCriado($produto)
    {
        try {
            logger('Produto criado via API: ' . $produto->nome);
        } catch (\Exception $e) {
            logger('Erro ao criar notificação de produto criado: ' . $e->getMessage());
        }
    }
}
