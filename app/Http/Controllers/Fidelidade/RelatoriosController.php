<?php

namespace App\Http\Controllers\Fidelidade;

use App\Http\Controllers\Controller;
use App\Models\Fidelidade\FidelidadeCarteira;
use App\Models\Fidelidade\FidelidadeCashbackTransacao;
use App\Models\Fidelidade\FidelidadeCupom;
use App\Models\Fidelidade\FidelidadeCupomUso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatoriosController extends Controller
{
    public function index()
    {
        try {
            $periodo = request('periodo', '30');
            $dataInicio = request('data_inicio') ? Carbon::parse(request('data_inicio')) : Carbon::now()->subDays($periodo);
            $dataFim = request('data_fim') ? Carbon::parse(request('data_fim')) : Carbon::now();

            // Estatísticas básicas
            $estatisticas = [
                'total_transacoes' => FidelidadeCashbackTransacao::whereBetween('created_at', [$dataInicio, $dataFim])->count(),
                'total_cashback' => FidelidadeCashbackTransacao::whereBetween('created_at', [$dataInicio, $dataFim])
                    ->where('tipo', 'credito')
                    ->sum('valor'),
                'clientes_ativos' => FidelidadeCarteira::count(),
                'cupons_utilizados' => FidelidadeCupomUso::whereBetween('created_at', [$dataInicio, $dataFim])->count(),
                'transacoes_credito' => FidelidadeCashbackTransacao::where('tipo', 'credito')->whereBetween('created_at', [$dataInicio, $dataFim])->count(),
                'transacoes_uso' => FidelidadeCashbackTransacao::where('tipo', 'uso')->whereBetween('created_at', [$dataInicio, $dataFim])->count(),
                'transacoes_estorno' => FidelidadeCashbackTransacao::where('tipo', 'estorno')->whereBetween('created_at', [$dataInicio, $dataFim])->count(),
            ];

            // Top clientes
            $topClientes = FidelidadeCarteira::orderBy('saldo_total_disponivel', 'desc')
                ->limit(10)
                ->get();

            return view('fidelidade.relatorios.index', compact('estatisticas', 'topClientes'));
        } catch (\Exception $e) {
            return view('fidelidade.relatorios.index', [
                'estatisticas' => [
                    'total_transacoes' => 0,
                    'total_cashback' => 0,
                    'clientes_ativos' => 0,
                    'cupons_utilizados' => 0,
                    'transacoes_credito' => 0,
                    'transacoes_uso' => 0,
                    'transacoes_estorno' => 0,
                ],
                'topClientes' => collect([])
            ]);
        }
    }

    public function dashboard()
    {
        $periodo = request('periodo', '30');
        $dataInicio = Carbon::now()->subDays($periodo);

        // Estatísticas Gerais
        $estatisticas = [
            'total_carteiras' => FidelidadeCarteira::count(),
            'carteiras_ativas' => FidelidadeCarteira::where('status', 'ativa')->count(),
            'total_cashback_disponivel' => FidelidadeCarteira::sum('saldo_cashback'),
            'total_transacoes' => FidelidadeCashbackTransacao::where('created_at', '>=', $dataInicio)->count(),
            'valor_cashback_distribuido' => FidelidadeCashbackTransacao::where('tipo_transacao', 'ganho')
                ->where('created_at', '>=', $dataInicio)
                ->sum('valor'),
            'valor_cashback_resgatado' => FidelidadeCashbackTransacao::where('tipo_transacao', 'resgate')
                ->where('created_at', '>=', $dataInicio)
                ->sum('valor'),
            'cupons_utilizados' => FidelidadeCupomUso::where('created_at', '>=', $dataInicio)->count(),
            'valor_descontos_cupons' => FidelidadeCupomUso::where('created_at', '>=', $dataInicio)->sum('valor_desconto')
        ];

        // Crescimento comparativo
        $periodoAnterior = Carbon::now()->subDays($periodo * 2)->startOfDay();
        $fimPeriodoAnterior = Carbon::now()->subDays($periodo)->endOfDay();

        $transacoesAnterior = FidelidadeCashbackTransacao::whereBetween('created_at', [$periodoAnterior, $fimPeriodoAnterior])->count();
        $crescimentoTransacoes = $transacoesAnterior > 0 ?
            round((($estatisticas['total_transacoes'] - $transacoesAnterior) / $transacoesAnterior) * 100, 2) : 0;

        $estatisticas['crescimento_transacoes'] = $crescimentoTransacoes;

        return view('fidelidade.relatorios.dashboard', compact('estatisticas', 'periodo'));
    }

    public function transacoes(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));
        $tipoTransacao = $request->get('tipo_transacao');
        $status = $request->get('status');

        // Relatório de transações
        $transacoes = FidelidadeCashbackTransacao::with(['carteira.cliente', 'carteira.empresa'])
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->when($tipoTransacao, function ($query, $tipo) {
                return $query->where('tipo_transacao', $tipo);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Resumo do período
        $resumo = [
            'total_transacoes' => $transacoes->total(),
            'valor_total' => FidelidadeCashbackTransacao::whereBetween('created_at', [$dataInicio, $dataFim])
                ->when($tipoTransacao, function ($query, $tipo) {
                    return $query->where('tipo_transacao', $tipo);
                })
                ->when($status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                ->sum('valor'),
            'por_tipo' => FidelidadeCashbackTransacao::selectRaw('tipo_transacao, COUNT(*) as total, SUM(valor) as valor_total')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->groupBy('tipo_transacao')
                ->get(),
            'por_status' => FidelidadeCashbackTransacao::selectRaw('status, COUNT(*) as total')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->groupBy('status')
                ->get()
        ];

        // Gráfico por dia
        $transacoesPorDia = FidelidadeCashbackTransacao::selectRaw('DATE(created_at) as data, COUNT(*) as total, SUM(valor) as valor_total')
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->when($tipoTransacao, function ($query, $tipo) {
                return $query->where('tipo_transacao', $tipo);
            })
            ->groupBy('data')
            ->orderBy('data')
            ->get();

        return view('fidelidade.relatorios.transacoes', compact(
            'transacoes',
            'resumo',
            'transacoesPorDia',
            'dataInicio',
            'dataFim',
            'tipoTransacao',
            'status'
        ));
    }

    public function clientes(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));
        $nivel = $request->get('nivel');
        $ordenacao = $request->get('ordenacao', 'saldo_desc');

        // Query base
        $query = FidelidadeCarteira::with(['cliente', 'empresa'])
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->when($nivel, function ($q, $nivel) {
                return $q->where('nivel_atual', $nivel);
            });

        // Ordenação
        switch ($ordenacao) {
            case 'saldo_desc':
                $query->orderBy('saldo_total_disponivel', 'desc');
                break;
            case 'saldo_asc':
                $query->orderBy('saldo_total_disponivel', 'asc');
                break;
            case 'transacoes_desc':
                $query->withCount('transacoesCashback')->orderBy('transacoes_cashback_count', 'desc');
                break;
            case 'cadastro_desc':
                $query->orderBy('created_at', 'desc');
                break;
        }

        $carteiras = $query->paginate(50);

        // Estatísticas
        $estatisticas = [
            'total_clientes' => FidelidadeCarteira::whereBetween('created_at', [$dataInicio, $dataFim])->count(),
            'clientes_ativos' => FidelidadeCarteira::where('status', 'ativa')
                ->whereBetween('created_at', [$dataInicio, $dataFim])->count(),
            'saldo_medio' => FidelidadeCarteira::whereBetween('created_at', [$dataInicio, $dataFim])
                ->avg('saldo_total_disponivel'),
            'por_nivel' => FidelidadeCarteira::selectRaw('nivel_atual, COUNT(*) as total, AVG(saldo_total_disponivel) as saldo_medio')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->groupBy('nivel_atual')
                ->get()
        ];

        return view('fidelidade.relatorios.clientes', compact(
            'carteiras',
            'estatisticas',
            'dataInicio',
            'dataFim',
            'nivel',
            'ordenacao'
        ));
    }

    public function cupons(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));
        $status = $request->get('status');

        // Relatório de cupons
        $cupons = FidelidadeCupom::with(['empresa'])
            ->withCount(['usos' => function ($query) use ($dataInicio, $dataFim) {
                $query->whereBetween('created_at', [$dataInicio, $dataFim]);
            }])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Estatísticas de uso
        $estatisticas = [
            'total_cupons' => FidelidadeCupom::count(),
            'cupons_ativos' => FidelidadeCupom::where('status', 'ativo')->count(),
            'usos_periodo' => FidelidadeCupomUso::whereBetween('created_at', [$dataInicio, $dataFim])->count(),
            'valor_total_descontos' => FidelidadeCupomUso::whereBetween('created_at', [$dataInicio, $dataFim])
                ->sum('valor_desconto'),
            'cupom_mais_usado' => FidelidadeCupomUso::selectRaw('cupom_id, COUNT(*) as total_usos')
                ->whereBetween('created_at', [$dataInicio, $dataFim])
                ->groupBy('cupom_id')
                ->orderBy('total_usos', 'desc')
                ->with('cupom')
                ->first()
        ];

        // Top cupons mais utilizados
        $topCupons = FidelidadeCupomUso::selectRaw('cupom_id, COUNT(*) as total_usos, SUM(valor_desconto) as total_descontos')
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->with('cupom')
            ->groupBy('cupom_id')
            ->orderBy('total_usos', 'desc')
            ->limit(10)
            ->get();

        return view('fidelidade.relatorios.cupons', compact(
            'cupons',
            'estatisticas',
            'topCupons',
            'dataInicio',
            'dataFim',
            'status'
        ));
    }

    public function performance(Request $request)
    {
        $periodo = $request->get('periodo', '30');
        $dataInicio = Carbon::now()->subDays($periodo);

        // Performance do programa de fidelidade
        $performance = [
            'novos_clientes' => FidelidadeCarteira::where('created_at', '>=', $dataInicio)->count(),
            'clientes_ativos' => FidelidadeCarteira::whereHas('transacoesCashback', function ($query) use ($dataInicio) {
                $query->where('created_at', '>=', $dataInicio);
            })->count(),
            'taxa_retencao' => 0, // Calcular baseado em transações repetidas
            'ticket_medio' => FidelidadeCashbackTransacao::where('tipo_transacao', 'ganho')
                ->where('created_at', '>=', $dataInicio)
                ->avg('valor'),
            'frequencia_uso' => 0, // Calcular média de transações por cliente
        ];

        // Distribuição por nível de cliente
        $distribuicaoNiveis = FidelidadeCarteira::selectRaw('nivel_atual, COUNT(*) as total')
            ->groupBy('nivel_atual')
            ->get();

        // ROI do programa (exemplo simplificado)
        $cashbackDistribuido = FidelidadeCashbackTransacao::where('tipo_transacao', 'ganho')
            ->where('created_at', '>=', $dataInicio)
            ->sum('valor');

        $vendasTotais = FidelidadeCashbackTransacao::where('created_at', '>=', $dataInicio)
            ->sum('valor_compra'); // Assumindo que existe este campo

        $roi = $vendasTotais > 0 ? (($vendasTotais - $cashbackDistribuido) / $cashbackDistribuido) * 100 : 0;

        return view('fidelidade.relatorios.performance', compact(
            'performance',
            'distribuicaoNiveis',
            'roi',
            'periodo',
            'cashbackDistribuido',
            'vendasTotais'
        ));
    }

    public function exportarTransacoes(Request $request)
    {
        $dataInicio = $request->get('data_inicio', Carbon::now()->subMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', Carbon::now()->format('Y-m-d'));

        $transacoes = FidelidadeCashbackTransacao::with(['carteira.cliente', 'carteira.empresa'])
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->orderBy('created_at', 'desc')
            ->get();

        $nomeArquivo = "relatorio_transacoes_{$dataInicio}_a_{$dataFim}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$nomeArquivo}\"",
        ];

        $callback = function () use ($transacoes) {
            $file = fopen('php://output', 'w');

            // Cabeçalho do CSV
            fputcsv($file, [
                'ID Transação',
                'Data/Hora',
                'Cliente',
                'Email Cliente',
                'Empresa',
                'Tipo Transação',
                'Valor (R$)',
                'Descrição',
                'Status',
                'Data Processamento',
                'Pedido ID'
            ]);

            foreach ($transacoes as $transacao) {
                fputcsv($file, [
                    $transacao->id,
                    $transacao->created_at->format('d/m/Y H:i:s'),
                    $transacao->carteira->cliente->name ?? 'N/A',
                    $transacao->carteira->cliente->email ?? 'N/A',
                    $transacao->carteira->empresa->name ?? 'N/A',
                    $transacao->tipo_transacao,
                    number_format($transacao->valor, 2, ',', '.'),
                    $transacao->descricao,
                    $transacao->status,
                    $transacao->data_processamento ? $transacao->data_processamento->format('d/m/Y H:i:s') : 'N/A',
                    $transacao->pedido_id ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportarClientes(Request $request)
    {
        $carteiras = FidelidadeCarteira::with(['cliente', 'empresa'])
            ->orderBy('created_at', 'desc')
            ->get();

        $nomeArquivo = "relatorio_clientes_fidelidade_" . Carbon::now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$nomeArquivo}\"",
        ];

        $callback = function () use ($carteiras) {
            $file = fopen('php://output', 'w');

            // Cabeçalho do CSV
            fputcsv($file, [
                'ID Carteira',
                'Cliente',
                'Email',
                'Empresa',
                'Saldo Cashback (R$)',
                'Saldo Créditos (R$)',
                'Saldo Total (R$)',
                'Nível Atual',
                'XP Total',
                'Status',
                'Data Cadastro'
            ]);

            foreach ($carteiras as $carteira) {
                fputcsv($file, [
                    $carteira->id,
                    $carteira->cliente->name ?? 'N/A',
                    $carteira->cliente->email ?? 'N/A',
                    $carteira->empresa->name ?? 'N/A',
                    number_format($carteira->saldo_cashback, 2, ',', '.'),
                    number_format($carteira->saldo_creditos, 2, ',', '.'),
                    number_format($carteira->saldo_total_disponivel, 2, ',', '.'),
                    $carteira->nivel_atual,
                    $carteira->xp_total,
                    $carteira->status,
                    $carteira->created_at->format('d/m/Y H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
