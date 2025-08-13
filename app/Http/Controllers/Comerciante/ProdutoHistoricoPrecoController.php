<?php

namespace App\Http\Controllers\Comerciante;

use App\Http\Controllers\Controller;
use App\Models\ProdutoHistoricoPreco;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProdutoHistoricoPrecoController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $query = ProdutoHistoricoPreco::with(['produto', 'usuario'])
            ->porEmpresa($empresaId);

        // Filtros
        if ($request->filled('produto_id')) {
            $query->porProduto($request->produto_id);
        }

        if ($request->filled('motivo')) {
            $query->porMotivo($request->motivo);
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $dataInicio = Carbon::createFromFormat('Y-m-d', $request->data_inicio)->startOfDay();
            $dataFim = Carbon::createFromFormat('Y-m-d', $request->data_fim)->endOfDay();
            $query->porPeriodo($dataInicio, $dataFim);
        }

        if ($request->filled('tipo_variacao')) {
            switch ($request->tipo_variacao) {
                case 'aumento':
                    $query->whereRaw('preco_novo > preco_anterior');
                    break;
                case 'reducao':
                    $query->whereRaw('preco_novo < preco_anterior');
                    break;
                case 'sem_alteracao':
                    $query->whereRaw('preco_novo = preco_anterior');
                    break;
            }
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->whereHas('produto', function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                    ->orWhere('sku', 'like', "%{$busca}%");
            });
        }

        $historicoPrecos = $query->ordenado()->paginate(20);

        // Para os filtros
        $produtos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();
        $motivos = ProdutoHistoricoPreco::getMotivos();

        return view('comerciantes.produtos.historico-precos.index', compact(
            'historicoPrecos',
            'produtos',
            'motivos'
        ));
    }

    public function show(ProdutoHistoricoPreco $historicoPreco)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($historicoPreco->empresa_id !== $empresaId) {
            abort(404);
        }

        $historicoPreco->load(['produto', 'usuario']);

        return view('comerciantes.produtos.historico-precos.show', compact('historicoPreco'));
    }

    // Relatório de análise de preços
    public function relatorio(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        // Período padrão: últimos 30 dias
        $dataInicio = $request->filled('data_inicio')
            ? Carbon::createFromFormat('Y-m-d', $request->data_inicio)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $dataFim = $request->filled('data_fim')
            ? Carbon::createFromFormat('Y-m-d', $request->data_fim)->endOfDay()
            : now()->endOfDay();

        // Estatísticas gerais
        $estatisticas = ProdutoHistoricoPreco::getEstatisticasPorPeriodo($empresaId, $dataInicio, $dataFim);

        // Produtos mais alterados
        $produtosMaisAlterados = ProdutoHistoricoPreco::getProdutosMaisAlterados($empresaId, 10, 30);

        // Variações por período
        $agrupamento = $request->get('agrupamento', 'dia');
        $variacoesPorPeriodo = ProdutoHistoricoPreco::getRelatorioVariacoes($empresaId, $dataInicio, $dataFim, $agrupamento);

        // Distribuição por motivo
        $distribuicaoPorMotivo = ProdutoHistoricoPreco::porEmpresa($empresaId)
            ->porPeriodo($dataInicio, $dataFim)
            ->selectRaw('motivo, COUNT(*) as total')
            ->groupBy('motivo')
            ->orderBy('total', 'desc')
            ->get();

        return view('comerciantes.produtos.historico-precos.relatorio', compact(
            'estatisticas',
            'produtosMaisAlterados',
            'variacoesPorPeriodo',
            'distribuicaoPorMotivo',
            'dataInicio',
            'dataFim',
            'agrupamento'
        ));
    }

    // Histórico de um produto específico
    public function produto(Request $request, Produto $produto)
    {
        // Verificar se pertence à empresa do usuário
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        if ($produto->empresa_id !== $empresaId) {
            abort(404);
        }

        $historicos = ProdutoHistoricoPreco::with(['usuario'])
            ->porProduto($produto->id)
            ->ordenado()
            ->paginate(20);

        // Estatísticas do produto
        $estatisticas = [
            'total_alteracoes' => $historicos->total(),
            'ultima_alteracao' => $historicos->first()?->data_alteracao,
            'preco_inicial' => $historicos->last()?->preco_anterior ?? $produto->preco_venda,
            'preco_atual' => $produto->preco_venda,
            'maior_preco' => $historicos->max('preco_novo') ?? $produto->preco_venda,
            'menor_preco' => $historicos->min('preco_novo') ?? $produto->preco_venda
        ];

        // Variação total
        if ($estatisticas['preco_inicial'] > 0) {
            $estatisticas['variacao_total_percentual'] =
                (($estatisticas['preco_atual'] - $estatisticas['preco_inicial']) / $estatisticas['preco_inicial']) * 100;
            $estatisticas['variacao_total_monetaria'] =
                $estatisticas['preco_atual'] - $estatisticas['preco_inicial'];
        } else {
            $estatisticas['variacao_total_percentual'] = 0;
            $estatisticas['variacao_total_monetaria'] = 0;
        }

        return view('comerciantes.produtos.historico-precos.produto', compact(
            'produto',
            'historicos',
            'estatisticas'
        ));
    }

    // Comparação entre produtos
    public function comparacao(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $produtos = collect();
        $historicos = collect();

        if ($request->filled('produtos')) {
            $produtoIds = $request->produtos;

            $produtos = Produto::porEmpresa($empresaId)
                ->whereIn('id', $produtoIds)
                ->get();

            $dataInicio = $request->filled('data_inicio')
                ? Carbon::createFromFormat('Y-m-d', $request->data_inicio)->startOfDay()
                : now()->subDays(30)->startOfDay();

            $dataFim = $request->filled('data_fim')
                ? Carbon::createFromFormat('Y-m-d', $request->data_fim)->endOfDay()
                : now()->endOfDay();

            $historicos = ProdutoHistoricoPreco::with(['produto'])
                ->porEmpresa($empresaId)
                ->whereIn('produto_id', $produtoIds)
                ->porPeriodo($dataInicio, $dataFim)
                ->ordenado()
                ->get()
                ->groupBy('produto_id');
        }

        $todosOsProdutos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();

        return view('comerciantes.produtos.historico-precos.comparacao', compact(
            'produtos',
            'historicos',
            'todosOsProdutos'
        ));
    }

    // AJAX: Dados para gráficos
    public function dadosGrafico(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);
        $produtoId = $request->produto_id;

        $historicos = ProdutoHistoricoPreco::porEmpresa($empresaId)
            ->when($produtoId, function ($query) use ($produtoId) {
                return $query->porProduto($produtoId);
            })
            ->ordenado('asc')
            ->get();

        $dados = $historicos->map(function ($item) {
            return [
                'data' => $item->data_alteracao->format('Y-m-d H:i'),
                'preco' => (float) $item->preco_novo,
                'produto' => $item->produto->nome
            ];
        });

        return response()->json($dados);
    }

    // Exportar histórico
    public function exportar(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $dataInicio = $request->filled('data_inicio')
            ? Carbon::createFromFormat('Y-m-d', $request->data_inicio)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $dataFim = $request->filled('data_fim')
            ? Carbon::createFromFormat('Y-m-d', $request->data_fim)->endOfDay()
            : now()->endOfDay();

        $dados = ProdutoHistoricoPreco::exportarHistorico($empresaId, $dataInicio, $dataFim);

        $nomeArquivo = 'historico_precos_' . $dataInicio->format('Y-m-d') . '_' . $dataFim->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $nomeArquivo . '"'
        ];

        $callback = function () use ($dados) {
            $file = fopen('php://output', 'w');

            // Cabeçalho
            fputcsv($file, [
                'Data',
                'Produto',
                'SKU',
                'Preço Anterior',
                'Preço Novo',
                'Variação',
                'Percentual',
                'Motivo',
                'Usuário',
                'Observações'
            ], ';');

            // Dados
            foreach ($dados as $linha) {
                fputcsv($file, array_values($linha), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Limpar histórico antigo
    public function limparAntigo(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $validated = $request->validate([
            'dias_manter' => 'required|integer|min:30|max:3650'
        ]);

        $registrosRemovidos = ProdutoHistoricoPreco::limparHistoricoAntigo(
            $empresaId,
            $validated['dias_manter']
        );

        return response()->json([
            'success' => true,
            'message' => "Foram removidos {$registrosRemovidos} registros antigos do histórico.",
            'registros_removidos' => $registrosRemovidos
        ]);
    }

    // Estatísticas rápidas para dashboard
    public function estatisticasRapidas()
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $hoje = now()->startOfDay();
        $ontem = now()->subDay()->startOfDay();
        $semanaPassada = now()->subWeek()->startOfDay();

        $estatisticas = [
            'alteracoes_hoje' => ProdutoHistoricoPreco::porEmpresa($empresaId)
                ->where('data_alteracao', '>=', $hoje)
                ->count(),

            'alteracoes_ontem' => ProdutoHistoricoPreco::porEmpresa($empresaId)
                ->whereBetween('data_alteracao', [$ontem, $hoje])
                ->count(),

            'alteracoes_semana' => ProdutoHistoricoPreco::porEmpresa($empresaId)
                ->where('data_alteracao', '>=', $semanaPassada)
                ->count(),

            'produtos_alterados_semana' => ProdutoHistoricoPreco::porEmpresa($empresaId)
                ->where('data_alteracao', '>=', $semanaPassada)
                ->distinct('produto_id')
                ->count()
        ];

        return response()->json($estatisticas);
    }

    public function create()
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $produtos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();

        return view('comerciantes.produtos.historico-precos.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'preco_venda_anterior' => 'required|numeric|min:0',
            'preco_venda_novo' => 'required|numeric|min:0',
            'preco_compra_anterior' => 'nullable|numeric|min:0',
            'preco_compra_novo' => 'nullable|numeric|min:0',
            'motivo' => 'nullable|string|max:255',
            'data_alteracao' => 'required|date'
        ]);

        $produto = Produto::porEmpresa($empresaId)->findOrFail($request->produto_id);

        ProdutoHistoricoPreco::create([
            'empresa_id' => $empresaId,
            'produto_id' => $request->produto_id,
            'preco_venda_anterior' => $request->preco_venda_anterior,
            'preco_venda_novo' => $request->preco_venda_novo,
            'preco_compra_anterior' => $request->preco_compra_anterior,
            'preco_compra_novo' => $request->preco_compra_novo,
            'margem_anterior' => $this->calcularMargem($request->preco_compra_anterior, $request->preco_venda_anterior),
            'margem_nova' => $this->calcularMargem($request->preco_compra_novo, $request->preco_venda_novo),
            'motivo' => $request->motivo,
            'usuario_id' => Auth::id(),
            'data_alteracao' => $request->data_alteracao,
        ]);

        return redirect()->route('comerciantes.produtos.historico-precos.index')
            ->with('success', 'Entrada de histórico criada com sucesso!');
    }

    public function edit(ProdutoHistoricoPreco $historicoPreco)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($historicoPreco->empresa_id !== $empresaId) {
            abort(404);
        }

        $produtos = Produto::porEmpresa($empresaId)->ativo()->orderBy('nome')->get();

        return view('comerciantes.produtos.historico-precos.edit', compact('historicoPreco', 'produtos'));
    }

    public function update(Request $request, ProdutoHistoricoPreco $historicoPreco)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($historicoPreco->empresa_id !== $empresaId) {
            abort(404);
        }

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'preco_venda_anterior' => 'required|numeric|min:0',
            'preco_venda_novo' => 'required|numeric|min:0',
            'preco_compra_anterior' => 'nullable|numeric|min:0',
            'preco_compra_novo' => 'nullable|numeric|min:0',
            'motivo' => 'nullable|string|max:255',
            'data_alteracao' => 'required|date'
        ]);

        $produto = Produto::porEmpresa($empresaId)->findOrFail($request->produto_id);

        $historicoPreco->update([
            'produto_id' => $request->produto_id,
            'preco_venda_anterior' => $request->preco_venda_anterior,
            'preco_venda_novo' => $request->preco_venda_novo,
            'preco_compra_anterior' => $request->preco_compra_anterior,
            'preco_compra_novo' => $request->preco_compra_novo,
            'margem_anterior' => $this->calcularMargem($request->preco_compra_anterior, $request->preco_venda_anterior),
            'margem_nova' => $this->calcularMargem($request->preco_compra_novo, $request->preco_venda_novo),
            'motivo' => $request->motivo,
            'data_alteracao' => $request->data_alteracao,
        ]);

        return redirect()->route('comerciantes.produtos.historico-precos.index')
            ->with('success', 'Histórico atualizado com sucesso!');
    }

    public function destroy(ProdutoHistoricoPreco $historicoPreco)
    {
        $empresaId = Auth::user()->empresa_id ?? session('empresa_id', 1);

        if ($historicoPreco->empresa_id !== $empresaId) {
            abort(404);
        }

        $historicoPreco->delete();

        return redirect()->route('comerciantes.produtos.historico-precos.index')
            ->with('success', 'Histórico excluído com sucesso!');
    }

    private function calcularMargem($precoCompra, $precoVenda)
    {
        if ($precoCompra <= 0) {
            return null;
        }

        return (($precoVenda - $precoCompra) / $precoCompra) * 100;
    }
}
