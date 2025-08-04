<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstatisticasController extends Controller
{
    public function index()
    {
        return view('admin.notificacoes.estatisticas');
    }

    public function dados(Request $request)
    {
        try {
            $periodo = $request->get('periodo', '7dias');
            $canal = $request->get('canal', '');
            $dataInicio = $request->get('data_inicio');
            $dataFim = $request->get('data_fim');

            // Define as datas baseado no período
            [$inicio, $fim] = $this->calcularPeriodo($periodo, $dataInicio, $dataFim);

            // Busca dados das notificações
            $dadosGerais = $this->obterDadosGerais($inicio, $fim, $canal);
            $volumeNotificacoes = $this->obterVolumeNotificacoes($inicio, $fim, $canal);
            $distribuicaoCanais = $this->obterDistribuicaoCanais($inicio, $fim);
            $tiposEventos = $this->obterTiposEventos($inicio, $fim, $canal);
            $performanceHoras = $this->obterPerformanceHoras($inicio, $fim, $canal);
            $templatesTop = $this->obterTemplatesTop($inicio, $fim, $canal);
            $ultimosErros = $this->obterUltimosErros();

            return response()->json([
                'success' => true,
                'dados' => [
                    'gerais' => $dadosGerais,
                    'volume' => $volumeNotificacoes,
                    'canais' => $distribuicaoCanais,
                    'tipos' => $tiposEventos,
                    'horas' => $performanceHoras,
                    'templates' => $templatesTop,
                    'erros' => $ultimosErros,
                    'periodo' => [
                        'inicio' => $inicio->format('Y-m-d'),
                        'fim' => $fim->format('Y-m-d')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calcularPeriodo($periodo, $dataInicio = null, $dataFim = null)
    {
        $fim = Carbon::now();

        switch ($periodo) {
            case 'hoje':
                $inicio = Carbon::today();
                break;
            case 'ontem':
                $inicio = Carbon::yesterday();
                $fim = Carbon::yesterday()->endOfDay();
                break;
            case '30dias':
                $inicio = Carbon::now()->subDays(30);
                break;
            case 'personalizado':
                if ($dataInicio && $dataFim) {
                    $inicio = Carbon::parse($dataInicio);
                    $fim = Carbon::parse($dataFim)->endOfDay();
                } else {
                    $inicio = Carbon::now()->subDays(7);
                }
                break;
            default: // 7dias
                $inicio = Carbon::now()->subDays(7);
                break;
        }

        return [$inicio, $fim];
    }

    private function obterDadosGerais($inicio, $fim, $canal)
    {
        $query = DB::table('notificacao_enviadas')
            ->whereBetween('created_at', [$inicio, $fim]);

        if ($canal) {
            $query->where('canal', $canal);
        }

        $total = $query->count();
        $enviadas = $query->where('status', 'enviado')->count();
        $falharam = $query->where('status', 'falhou')->count();
        $pendentes = $query->where('status', 'pendente')->count();

        $taxaSucesso = $total > 0 ? round(($enviadas / $total) * 100, 1) : 0;
        $taxaErro = $total > 0 ? round(($falharam / $total) * 100, 1) : 0;

        // Calcula tempo médio de processamento
        $tempoMedio = DB::table('notificacao_enviadas')
            ->whereBetween('created_at', [$inicio, $fim])
            ->whereNotNull('tempo_processamento')
            ->avg('tempo_processamento');

        $tempoMedio = $tempoMedio ? round($tempoMedio) . 'ms' : '0ms';

        return [
            'total_enviadas' => number_format($total, 0, ',', '.'),
            'taxa_sucesso' => $taxaSucesso . '%',
            'tempo_medio' => $tempoMedio,
            'taxa_erro' => $taxaErro . '%',
            'detalhes' => [
                'enviadas' => $enviadas,
                'falharam' => $falharam,
                'pendentes' => $pendentes
            ]
        ];
    }

    private function obterVolumeNotificacoes($inicio, $fim, $canal)
    {
        $query = DB::table('notificacao_enviadas')
            ->select(
                DB::raw('DATE(created_at) as data'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "enviado" THEN 1 ELSE 0 END) as enviadas'),
                DB::raw('SUM(CASE WHEN status = "falhou" THEN 1 ELSE 0 END) as falharam')
            )
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('data');

        if ($canal) {
            $query->where('canal', $canal);
        }

        $dados = $query->get();

        $labels = [];
        $enviadas = [];
        $falharam = [];

        foreach ($dados as $dia) {
            $labels[] = Carbon::parse($dia->data)->format('d/m');
            $enviadas[] = (int) $dia->enviadas;
            $falharam[] = (int) $dia->falharam;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Enviadas',
                    'data' => $enviadas,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)'
                ],
                [
                    'label' => 'Falharam',
                    'data' => $falharam,
                    'borderColor' => 'rgb(255, 99, 132)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)'
                ]
            ]
        ];
    }

    private function obterDistribuicaoCanais($inicio, $fim)
    {
        $dados = DB::table('notificacao_enviadas')
            ->select('canal', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupBy('canal')
            ->orderBy('total', 'desc')
            ->get();

        $labels = [];
        $valores = [];
        $cores = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)'
        ];

        foreach ($dados as $index => $canal) {
            $labels[] = ucfirst($canal->canal);
            $valores[] = (int) $canal->total;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $valores,
                    'backgroundColor' => array_slice($cores, 0, count($valores)),
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]
            ]
        ];
    }

    private function obterTiposEventos($inicio, $fim, $canal)
    {
        $query = DB::table('notificacao_enviadas as ne')
            ->join('notificacao_aplicacoes as na', 'ne.aplicacao_id', '=', 'na.id')
            ->select('na.nome', DB::raw('COUNT(*) as total'))
            ->whereBetween('ne.created_at', [$inicio, $fim])
            ->groupBy('na.nome')
            ->orderBy('total', 'desc')
            ->limit(10);

        if ($canal) {
            $query->where('ne.canal', $canal);
        }

        $dados = $query->get();

        $labels = [];
        $valores = [];
        $cores = [
            'rgba(54, 162, 235, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)',
            'rgba(83, 102, 255, 0.8)',
            'rgba(255, 99, 255, 0.8)',
            'rgba(99, 255, 132, 0.8)'
        ];

        foreach ($dados as $index => $tipo) {
            $labels[] = $tipo->nome;
            $valores[] = (int) $tipo->total;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Notificações',
                    'data' => $valores,
                    'backgroundColor' => array_slice($cores, 0, count($valores)),
                    'borderColor' => array_map(function ($cor) {
                        return str_replace('0.8', '1', $cor);
                    }, array_slice($cores, 0, count($valores))),
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    private function obterPerformanceHoras($inicio, $fim, $canal)
    {
        $query = DB::table('notificacao_enviadas')
            ->select(
                DB::raw('HOUR(created_at) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hora');

        if ($canal) {
            $query->where('canal', $canal);
        }

        $dados = $query->get();

        // Cria array com todas as horas (0-23)
        $horas = [];
        $valores = [];

        for ($h = 0; $h < 24; $h += 4) {
            $horas[] = sprintf('%02dh', $h);
            $valorHora = $dados->firstWhere('hora', $h);
            $valores[] = $valorHora ? (int) $valorHora->total : 0;
        }

        return [
            'labels' => $horas,
            'datasets' => [
                [
                    'label' => 'Notificações por Hora',
                    'data' => $valores,
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                    'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
                    'tension' => 0.4,
                    'fill' => true,
                    'pointBackgroundColor' => 'rgba(255, 159, 64, 1)',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2
                ]
            ]
        ];
    }

    private function obterTemplatesTop($inicio, $fim, $canal)
    {
        $query = DB::table('notificacao_enviadas as ne')
            ->join('notificacao_templates as nt', 'ne.template_id', '=', 'nt.id')
            ->select(
                'nt.nome',
                'ne.canal',
                DB::raw('COUNT(*) as enviados'),
                DB::raw('ROUND((SUM(CASE WHEN ne.status = "enviado" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as taxa_sucesso')
            )
            ->whereBetween('ne.created_at', [$inicio, $fim])
            ->groupBy('nt.nome', 'ne.canal')
            ->orderBy('enviados', 'desc')
            ->limit(10);

        if ($canal) {
            $query->where('ne.canal', $canal);
        }

        return $query->get()->map(function ($item) {
            return [
                'template' => $item->nome,
                'canal' => $item->canal,
                'enviados' => number_format($item->enviados, 0, ',', '.'),
                'taxa_sucesso' => $item->taxa_sucesso . '%'
            ];
        });
    }

    private function obterUltimosErros()
    {
        return DB::table('notificacao_enviadas')
            ->select('created_at', 'canal', 'erro_detalhes', 'tentativas')
            ->where('status', 'falhou')
            ->whereNotNull('erro_detalhes')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($erro) {
                return [
                    'horario' => Carbon::parse($erro->created_at)->format('H:i'),
                    'canal' => $erro->canal,
                    'erro' => $erro->erro_detalhes ? substr($erro->erro_detalhes, 0, 50) . '...' : 'Erro desconhecido',
                    'tentativas' => $erro->tentativas . '/3'
                ];
            });
    }
}
