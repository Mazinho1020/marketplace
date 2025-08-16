<?php

namespace App\Http\Controllers\Comerciantes\Vendas;

use App\Http\Controllers\Controller;
use App\Models\Vendas\Venda;
use App\Models\Vendas\VendaItem;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardVendaController extends Controller
{
    /**
     * Show sales dashboard
     */
    public function index(int $empresa)
    {
        try {
            $hoje = Carbon::today();
            $ontem = Carbon::yesterday();
            $inicioMes = Carbon::now()->startOfMonth();
            $inicioAno = Carbon::now()->startOfYear();

            // Estatísticas principais
            $estatisticas = [
                // Vendas de hoje
                'vendas_hoje_quantidade' => Venda::porEmpresa($empresa)
                    ->whereDate('data_venda', $hoje)
                    ->count(),
                'vendas_hoje_valor' => Venda::porEmpresa($empresa)
                    ->whereDate('data_venda', $hoje)
                    ->sum('valor_liquido'),

                // Vendas de ontem
                'vendas_ontem_quantidade' => Venda::porEmpresa($empresa)
                    ->whereDate('data_venda', $ontem)
                    ->count(),
                'vendas_ontem_valor' => Venda::porEmpresa($empresa)
                    ->whereDate('data_venda', $ontem)
                    ->sum('valor_liquido'),

                // Vendas do mês
                'vendas_mes_quantidade' => Venda::porEmpresa($empresa)
                    ->where('data_venda', '>=', $inicioMes)
                    ->count(),
                'vendas_mes_valor' => Venda::porEmpresa($empresa)
                    ->where('data_venda', '>=', $inicioMes)
                    ->sum('valor_liquido'),

                // Vendas do ano
                'vendas_ano_quantidade' => Venda::porEmpresa($empresa)
                    ->where('data_venda', '>=', $inicioAno)
                    ->count(),
                'vendas_ano_valor' => Venda::porEmpresa($empresa)
                    ->where('data_venda', '>=', $inicioAno)
                    ->sum('valor_liquido'),

                // Ticket médio
                'ticket_medio_hoje' => $this->calcularTicketMedio($empresa, $hoje, $hoje),
                'ticket_medio_mes' => $this->calcularTicketMedio($empresa, $inicioMes),
                'ticket_medio_ano' => $this->calcularTicketMedio($empresa, $inicioAno),
            ];

            // Vendas por status
            $vendasPorStatus = Venda::porEmpresa($empresa)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Top produtos mais vendidos no mês
            $topProdutos = VendaItem::join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
                ->join('produtos', 'venda_itens.produto_id', '=', 'produtos.id')
                ->where('vendas.empresa_id', $empresa)
                ->where('vendas.data_venda', '>=', $inicioMes)
                ->select(
                    'produtos.nome',
                    DB::raw('SUM(venda_itens.quantidade) as total_vendido'),
                    DB::raw('SUM(venda_itens.valor_total) as total_faturado')
                )
                ->groupBy('produtos.id', 'produtos.nome')
                ->orderBy('total_vendido', 'desc')
                ->limit(10)
                ->get();

            // Vendas dos últimos 30 dias (para gráfico)
            $vendasUltimos30Dias = Venda::porEmpresa($empresa)
                ->where('data_venda', '>=', Carbon::now()->subDays(30))
                ->select(
                    DB::raw('DATE(data_venda) as data'),
                    DB::raw('COUNT(*) as quantidade'),
                    DB::raw('SUM(valor_liquido) as valor')
                )
                ->groupBy('data')
                ->orderBy('data')
                ->get();

            // Vendas por tipo no mês
            $vendasPorTipo = Venda::porEmpresa($empresa)
                ->where('data_venda', '>=', $inicioMes)
                ->select('tipo_venda', DB::raw('count(*) as total'))
                ->groupBy('tipo_venda')
                ->pluck('total', 'tipo_venda')
                ->toArray();

            // Vendas por hora hoje (para identificar picos)
            $vendasPorHora = Venda::porEmpresa($empresa)
                ->whereDate('data_venda', $hoje)
                ->select(
                    DB::raw('HOUR(data_venda) as hora'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('hora')
                ->orderBy('hora')
                ->get();

            return view('comerciantes.vendas.dashboard', compact(
                'empresa',
                'estatisticas',
                'vendasPorStatus',
                'topProdutos',
                'vendasUltimos30Dias',
                'vendasPorTipo',
                'vendasPorHora'
            ));

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar dashboard de vendas: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar dashboard de vendas.');
        }
    }

    /**
     * Get sales data for charts (AJAX)
     */
    public function dadosGrafico(Request $request, int $empresa)
    {
        try {
            $periodo = $request->get('periodo', '30_dias');
            
            switch ($periodo) {
                case '7_dias':
                    $dataInicio = Carbon::now()->subDays(7);
                    $agrupamento = 'DATE(data_venda)';
                    break;
                case '30_dias':
                    $dataInicio = Carbon::now()->subDays(30);
                    $agrupamento = 'DATE(data_venda)';
                    break;
                case '3_meses':
                    $dataInicio = Carbon::now()->subMonths(3);
                    $agrupamento = 'DATE_FORMAT(data_venda, "%Y-%m")';
                    break;
                case '12_meses':
                    $dataInicio = Carbon::now()->subMonths(12);
                    $agrupamento = 'DATE_FORMAT(data_venda, "%Y-%m")';
                    break;
                default:
                    $dataInicio = Carbon::now()->subDays(30);
                    $agrupamento = 'DATE(data_venda)';
            }

            $dados = Venda::porEmpresa($empresa)
                ->where('data_venda', '>=', $dataInicio)
                ->select(
                    DB::raw("{$agrupamento} as periodo"),
                    DB::raw('COUNT(*) as quantidade'),
                    DB::raw('SUM(valor_liquido) as valor')
                )
                ->groupBy('periodo')
                ->orderBy('periodo')
                ->get();

            return response()->json([
                'success' => true,
                'dados' => $dados
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar dados do gráfico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados do gráfico.'
            ], 500);
        }
    }

    /**
     * Get top products data (AJAX)
     */
    public function topProdutos(Request $request, int $empresa)
    {
        try {
            $periodo = $request->get('periodo', '30_dias');
            $limite = $request->get('limite', 10);

            switch ($periodo) {
                case 'hoje':
                    $dataInicio = Carbon::today();
                    break;
                case '7_dias':
                    $dataInicio = Carbon::now()->subDays(7);
                    break;
                case '30_dias':
                    $dataInicio = Carbon::now()->subDays(30);
                    break;
                case 'mes_atual':
                    $dataInicio = Carbon::now()->startOfMonth();
                    break;
                default:
                    $dataInicio = Carbon::now()->subDays(30);
            }

            $produtos = VendaItem::join('vendas', 'venda_itens.venda_id', '=', 'vendas.id')
                ->join('produtos', 'venda_itens.produto_id', '=', 'produtos.id')
                ->where('vendas.empresa_id', $empresa)
                ->where('vendas.data_venda', '>=', $dataInicio)
                ->select(
                    'produtos.nome',
                    'produtos.codigo_sistema',
                    DB::raw('SUM(venda_itens.quantidade) as total_vendido'),
                    DB::raw('SUM(venda_itens.valor_total) as total_faturado'),
                    DB::raw('COUNT(DISTINCT vendas.id) as numero_vendas')
                )
                ->groupBy('produtos.id', 'produtos.nome', 'produtos.codigo_sistema')
                ->orderBy('total_vendido', 'desc')
                ->limit($limite)
                ->get();

            return response()->json([
                'success' => true,
                'produtos' => $produtos
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar top produtos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar top produtos.'
            ], 500);
        }
    }

    /**
     * Calculate average ticket
     */
    private function calcularTicketMedio(int $empresa, Carbon $dataInicio, Carbon $dataFim = null): float
    {
        $query = Venda::porEmpresa($empresa)
            ->where('data_venda', '>=', $dataInicio);

        if ($dataFim) {
            $query->where('data_venda', '<=', $dataFim);
        }

        $totalVendas = $query->count();
        $valorTotal = $query->sum('valor_liquido');

        return $totalVendas > 0 ? round($valorTotal / $totalVendas, 2) : 0;
    }

    /**
     * Export sales report
     */
    public function exportarRelatorio(Request $request, int $empresa)
    {
        try {
            $formato = $request->get('formato', 'excel');
            $dataInicio = $request->get('data_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

            $vendas = Venda::porEmpresa($empresa)
                ->whereBetween('data_venda', [$dataInicio, $dataFim])
                ->with(['usuario', 'cliente', 'itens.produto'])
                ->orderBy('data_venda')
                ->get();

            if ($formato === 'pdf') {
                return $this->exportarPDF($vendas, $empresa, $dataInicio, $dataFim);
            } else {
                return $this->exportarExcel($vendas, $empresa, $dataInicio, $dataFim);
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao exportar relatório: ' . $e->getMessage());
            return back()->with('error', 'Erro ao exportar relatório.');
        }
    }

    /**
     * Export to Excel (simple CSV for now)
     */
    private function exportarExcel($vendas, $empresa, $dataInicio, $dataFim)
    {
        $filename = "vendas_empresa_{$empresa}_{$dataInicio}_a_{$dataFim}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($vendas) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho
            fputcsv($file, [
                'Número da Venda',
                'Data',
                'Cliente',
                'Tipo',
                'Status',
                'Valor Total',
                'Valor Desconto',
                'Valor Líquido',
                'Usuário',
                'Observações'
            ]);

            // Dados
            foreach ($vendas as $venda) {
                fputcsv($file, [
                    $venda->numero_venda,
                    $venda->data_venda->format('d/m/Y H:i'),
                    $venda->cliente ? $venda->cliente->nome : '-',
                    $venda->tipo_venda_formatado,
                    $venda->status_formatado,
                    number_format($venda->valor_total, 2, ',', '.'),
                    number_format($venda->valor_desconto, 2, ',', '.'),
                    number_format($venda->valor_liquido, 2, ',', '.'),
                    $venda->usuario ? $venda->usuario->name : '-',
                    $venda->observacoes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF (simple HTML for now)
     */
    private function exportarPDF($vendas, $empresa, $dataInicio, $dataFim)
    {
        return view('comerciantes.vendas.relatorio-pdf', compact('vendas', 'empresa', 'dataInicio', 'dataFim'));
    }
}