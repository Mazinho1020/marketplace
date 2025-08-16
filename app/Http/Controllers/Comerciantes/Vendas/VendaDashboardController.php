<?php

namespace App\Http\Controllers\Comerciantes\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Venda;
use App\Models\VendaItem;
use App\Services\Vendas\VendaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Controller de Dashboard e Relatórios de Vendas
 * 
 * Responsável por exibir métricas, relatórios e analytics
 * das vendas do marketplace
 */
class VendaDashboardController extends Controller
{
    protected $vendaService;

    public function __construct(VendaService $vendaService)
    {
        $this->vendaService = $vendaService;
    }

    /**
     * Dashboard principal de vendas
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id;

            // Período selecionado (default: últimos 30 dias)
            $periodo = $request->get('periodo', '30d');
            $dataCustomInicio = $request->get('data_inicio');
            $dataCustomFim = $request->get('data_fim');

            // Calcular datas baseado no período
            [$dataInicio, $dataFim] = $this->calcularPeriodo($periodo, $dataCustomInicio, $dataCustomFim);

            // Métricas principais
            $metricas = $this->vendaService->calcularMetricasVendas($empresaId, $dataInicio, $dataFim);

            // Métricas do período anterior para comparação
            $diasPeriodo = $dataInicio->diffInDays($dataFim);
            $dataInicioAnterior = $dataInicio->copy()->subDays($diasPeriodo);
            $dataFimAnterior = $dataInicio->copy()->subDay();
            $metricasAnteriores = $this->vendaService->calcularMetricasVendas($empresaId, $dataInicioAnterior, $dataFimAnterior);

            // Vendas por dia (gráfico)
            $vendasPorDia = $this->obterVendasPorDia($empresaId, $dataInicio, $dataFim);

            // Vendas por hora (gráfico)
            $vendasPorHora = $this->obterVendasPorHora($empresaId, $dataInicio, $dataFim);

            // Top produtos
            $topProdutos = $this->obterTopProdutos($empresaId, $dataInicio, $dataFim, 10);

            // Top clientes
            $topClientes = $this->obterTopClientes($empresaId, $dataInicio, $dataFim, 10);

            // Relatório de comissões
            $relatorioComissoes = $this->vendaService->obterRelatorioComissoes($empresaId, $dataInicio, $dataFim);

            // Últimas vendas
            $ultimasVendas = Venda::where('empresa_id', $empresaId)
                ->with(['cliente', 'usuario', 'itens'])
                ->orderBy('data_venda', 'desc')
                ->limit(10)
                ->get();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'metricas' => $metricas,
                    'metricas_anteriores' => $metricasAnteriores,
                    'vendas_por_dia' => $vendasPorDia,
                    'vendas_por_hora' => $vendasPorHora,
                    'top_produtos' => $topProdutos,
                    'top_clientes' => $topClientes,
                    'relatorio_comissoes' => $relatorioComissoes,
                    'ultimas_vendas' => $ultimasVendas
                ]);
            }

            return view('comerciantes.vendas.dashboard', compact(
                'metricas',
                'metricasAnteriores',
                'vendasPorDia',
                'vendasPorHora',
                'topProdutos',
                'topClientes',
                'relatorioComissoes',
                'ultimasVendas',
                'periodo',
                'dataInicio',
                'dataFim'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao carregar dashboard de vendas', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'empresa_id' => $empresaId ?? null
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao carregar dashboard: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao carregar dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Relatório detalhado de vendas
     */
    public function relatorioVendas(Request $request)
    {
        try {
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id;

            $dataInicio = Carbon::parse($request->get('data_inicio', now()->subDays(30)));
            $dataFim = Carbon::parse($request->get('data_fim', now()));
            $vendedorId = $request->get('vendedor_id');
            $clienteId = $request->get('cliente_id');
            $tipoVenda = $request->get('tipo_venda');

            $filtros = array_filter([
                'vendedor_id' => $vendedorId,
                'cliente_id' => $clienteId,
                'tipo_venda' => $tipoVenda,
            ]);

            $vendas = $this->vendaService->obterVendasPorPeriodo($empresaId, $dataInicio, $dataFim, $filtros)
                ->with(['cliente', 'usuario', 'itens.produto'])
                ->get();

            $metricas = $this->vendaService->calcularMetricasVendas($empresaId, $dataInicio, $dataFim);

            // Agrupar por diferentes critérios
            $vendasPorVendedor = $vendas->groupBy('usuario_id')->map(function ($grupo) {
                return [
                    'vendedor' => $grupo->first()->usuario->name ?? 'N/A',
                    'quantidade' => $grupo->count(),
                    'valor_total' => $grupo->sum('valor_total'),
                    'ticket_medio' => $grupo->count() > 0 ? $grupo->sum('valor_total') / $grupo->count() : 0
                ];
            });

            $vendasPorTipo = $vendas->groupBy('tipo_venda')->map(function ($grupo) {
                return [
                    'quantidade' => $grupo->count(),
                    'valor_total' => $grupo->sum('valor_total'),
                    'percentual' => 0 // Será calculado na view
                ];
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'vendas' => $vendas,
                    'metricas' => $metricas,
                    'vendas_por_vendedor' => $vendasPorVendedor,
                    'vendas_por_tipo' => $vendasPorTipo
                ]);
            }

            return view('comerciantes.vendas.relatorio-vendas', compact(
                'vendas',
                'metricas',
                'vendasPorVendedor',
                'vendasPorTipo',
                'dataInicio',
                'dataFim',
                'vendedorId',
                'clienteId',
                'tipoVenda'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório de vendas', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'empresa_id' => $empresaId ?? null
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    /**
     * Relatório de produtos
     */
    public function relatorioProdutos(Request $request)
    {
        try {
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id;

            $dataInicio = Carbon::parse($request->get('data_inicio', now()->subDays(30)));
            $dataFim = Carbon::parse($request->get('data_fim', now()));

            // Produtos mais vendidos
            $produtosMaisVendidos = VendaItem::join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
                ->join('produtos', 'venda_itens.produto_id', '=', 'produtos.id')
                ->where('vendas.empresa_id', $empresaId)
                ->whereBetween('vendas.data_venda', [$dataInicio, $dataFim])
                ->whereIn('vendas.status_venda', [Venda::STATUS_CONFIRMADA, Venda::STATUS_PAGA, Venda::STATUS_ENTREGUE, Venda::STATUS_FINALIZADA])
                ->select(
                    'produtos.id',
                    'produtos.nome',
                    'produtos.codigo_sistema',
                    'produtos.preco_venda',
                    DB::raw('SUM(venda_itens.quantidade) as total_vendido'),
                    DB::raw('SUM(venda_itens.valor_total_item) as receita_total'),
                    DB::raw('AVG(venda_itens.valor_unitario) as preco_medio'),
                    DB::raw('COUNT(DISTINCT vendas.id) as numero_vendas')
                )
                ->groupBy('produtos.id', 'produtos.nome', 'produtos.codigo_sistema', 'produtos.preco_venda')
                ->orderBy('total_vendido', 'desc')
                ->limit(50)
                ->get();

            // Produtos por categoria
            $produtosPorCategoria = VendaItem::join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
                ->join('produtos', 'venda_itens.produto_id', '=', 'produtos.id')
                ->leftJoin('produto_categorias', 'produtos.categoria_id', '=', 'produto_categorias.id')
                ->where('vendas.empresa_id', $empresaId)
                ->whereBetween('vendas.data_venda', [$dataInicio, $dataFim])
                ->whereIn('vendas.status_venda', [Venda::STATUS_CONFIRMADA, Venda::STATUS_PAGA, Venda::STATUS_ENTREGUE, Venda::STATUS_FINALIZADA])
                ->select(
                    'produto_categorias.nome as categoria',
                    DB::raw('SUM(venda_itens.quantidade) as total_vendido'),
                    DB::raw('SUM(venda_itens.valor_total_item) as receita_total'),
                    DB::raw('COUNT(DISTINCT produtos.id) as produtos_diferentes')
                )
                ->groupBy('produto_categorias.id', 'produto_categorias.nome')
                ->orderBy('receita_total', 'desc')
                ->get();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'produtos_mais_vendidos' => $produtosMaisVendidos,
                    'produtos_por_categoria' => $produtosPorCategoria
                ]);
            }

            return view('comerciantes.vendas.relatorio-produtos', compact(
                'produtosMaisVendidos',
                'produtosPorCategoria',
                'dataInicio',
                'dataFim'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório de produtos', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'empresa_id' => $empresaId ?? null
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    /**
     * Relatório financeiro
     */
    public function relatorioFinanceiro(Request $request)
    {
        try {
            $user = Auth::guard('comerciante')->user();
            $empresaId = $user->empresa_id;

            $dataInicio = Carbon::parse($request->get('data_inicio', now()->subDays(30)));
            $dataFim = Carbon::parse($request->get('data_fim', now()));

            // Receita por forma de pagamento
            $receitaPorFormaPagamento = DB::table('venda_pagamentos')
                ->join('vendas', 'venda_pagamentos.venda_id', '=', 'vendas.id')
                ->join('formas_pagamento', 'venda_pagamentos.forma_pagamento_id', '=', 'formas_pagamento.id')
                ->where('vendas.empresa_id', $empresaId)
                ->whereBetween('vendas.data_venda', [$dataInicio, $dataFim])
                ->where('venda_pagamentos.status_pagamento', 'confirmado')
                ->select(
                    'formas_pagamento.nome as forma_pagamento',
                    DB::raw('SUM(venda_pagamentos.valor_pagamento) as total_recebido'),
                    DB::raw('SUM(venda_pagamentos.valor_taxa) as total_taxas'),
                    DB::raw('SUM(venda_pagamentos.valor_liquido) as total_liquido'),
                    DB::raw('COUNT(*) as numero_transacoes')
                )
                ->groupBy('formas_pagamento.id', 'formas_pagamento.nome')
                ->orderBy('total_recebido', 'desc')
                ->get();

            // Comissões do marketplace
            $totalComissoesMarketplace = Venda::where('empresa_id', $empresaId)
                ->whereBetween('data_venda', [$dataInicio, $dataFim])
                ->whereIn('status_venda', [Venda::STATUS_CONFIRMADA, Venda::STATUS_PAGA, Venda::STATUS_ENTREGUE, Venda::STATUS_FINALIZADA])
                ->sum('valor_comissao_marketplace');

            // Relatório de comissões detalhado
            $relatorioComissoes = $this->vendaService->obterRelatorioComissoes($empresaId, $dataInicio, $dataFim);

            // Resumo financeiro
            $resumoFinanceiro = [
                'receita_bruta' => $receitaPorFormaPagamento->sum('total_recebido'),
                'total_taxas' => $receitaPorFormaPagamento->sum('total_taxas'),
                'receita_liquida' => $receitaPorFormaPagamento->sum('total_liquido'),
                'comissoes_marketplace' => $totalComissoesMarketplace,
                'numero_transacoes' => $receitaPorFormaPagamento->sum('numero_transacoes')
            ];

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'receita_por_forma_pagamento' => $receitaPorFormaPagamento,
                    'relatorio_comissoes' => $relatorioComissoes,
                    'resumo_financeiro' => $resumoFinanceiro
                ]);
            }

            return view('comerciantes.vendas.relatorio-financeiro', compact(
                'receitaPorFormaPagamento',
                'relatorioComissoes',
                'resumoFinanceiro',
                'dataInicio',
                'dataFim'
            ));

        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório financeiro', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'empresa_id' => $empresaId ?? null
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    /**
     * Métodos auxiliares
     */
    private function calcularPeriodo($periodo, $dataCustomInicio = null, $dataCustomFim = null)
    {
        if ($periodo === 'custom' && $dataCustomInicio && $dataCustomFim) {
            return [Carbon::parse($dataCustomInicio), Carbon::parse($dataCustomFim)];
        }

        $hoje = Carbon::now();
        
        switch ($periodo) {
            case '7d':
                return [$hoje->copy()->subDays(7), $hoje];
            case '30d':
                return [$hoje->copy()->subDays(30), $hoje];
            case '3m':
                return [$hoje->copy()->subMonths(3), $hoje];
            case '6m':
                return [$hoje->copy()->subMonths(6), $hoje];
            case '1y':
                return [$hoje->copy()->subYear(), $hoje];
            default:
                return [$hoje->copy()->subDays(30), $hoje];
        }
    }

    private function obterVendasPorDia($empresaId, $dataInicio, $dataFim)
    {
        return Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->confirmadas()
            ->select(
                DB::raw('DATE(data_venda) as data'),
                DB::raw('COUNT(*) as total_vendas'),
                DB::raw('SUM(valor_total) as valor_total')
            )
            ->groupBy(DB::raw('DATE(data_venda)'))
            ->orderBy('data')
            ->get();
    }

    private function obterVendasPorHora($empresaId, $dataInicio, $dataFim)
    {
        return Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->confirmadas()
            ->select(
                DB::raw('HOUR(data_venda) as hora'),
                DB::raw('COUNT(*) as total_vendas'),
                DB::raw('SUM(valor_total) as valor_total')
            )
            ->groupBy(DB::raw('HOUR(data_venda)'))
            ->orderBy('hora')
            ->get();
    }

    private function obterTopProdutos($empresaId, $dataInicio, $dataFim, $limit = 10)
    {
        return VendaItem::join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
            ->where('vendas.empresa_id', $empresaId)
            ->whereBetween('vendas.data_venda', [$dataInicio, $dataFim])
            ->whereIn('vendas.status_venda', [Venda::STATUS_CONFIRMADA, Venda::STATUS_PAGA, Venda::STATUS_ENTREGUE, Venda::STATUS_FINALIZADA])
            ->select(
                'venda_itens.produto_id',
                'venda_itens.nome_produto',
                DB::raw('SUM(venda_itens.quantidade) as total_vendido'),
                DB::raw('SUM(venda_itens.valor_total_item) as receita_total')
            )
            ->groupBy('venda_itens.produto_id', 'venda_itens.nome_produto')
            ->orderBy('total_vendido', 'desc')
            ->limit($limit)
            ->get();
    }

    private function obterTopClientes($empresaId, $dataInicio, $dataFim, $limit = 10)
    {
        return Venda::where('empresa_id', $empresaId)
            ->whereBetween('data_venda', [$dataInicio, $dataFim])
            ->whereNotNull('cliente_id')
            ->confirmadas()
            ->join('pessoas', 'vendas.cliente_id', '=', 'pessoas.id')
            ->select(
                'pessoas.id',
                'pessoas.nome',
                DB::raw('COUNT(vendas.id) as total_compras'),
                DB::raw('SUM(vendas.valor_total) as valor_total'),
                DB::raw('AVG(vendas.valor_total) as ticket_medio')
            )
            ->groupBy('pessoas.id', 'pessoas.nome')
            ->orderBy('valor_total', 'desc')
            ->limit($limit)
            ->get();
    }
}