<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogsController extends Controller
{
    public function index()
    {
        return view('admin.notificacoes.logs');
    }

    public function apiLogs(Request $request)
    {
        try {
            // Parâmetros de filtro
            $nivel = $request->get('nivel');
            $componente = $request->get('componente');
            $periodo = $request->get('periodo', '1h');
            $busca = $request->get('busca');
            $limite = $request->get('limite', 50);

            // Calcular período
            $dataInicio = $this->calcularDataInicio($periodo);

            // Query base dos logs
            $query = DB::table('notificacao_logs as nl')
                ->leftJoin('notificacao_enviadas as ne', 'nl.notificacao_id', '=', 'ne.id')
                ->select([
                    'nl.id',
                    'nl.nivel',
                    'nl.mensagem',
                    'nl.dados',
                    'nl.created_at',
                    'ne.canal as componente',
                    'ne.email_destinatario',
                    'ne.telefone_destinatario',
                    'ne.status',
                    'ne.titulo'
                ])
                ->where('nl.created_at', '>=', $dataInicio)
                ->orderBy('nl.created_at', 'desc');

            // Aplicar filtros
            if ($nivel) {
                $query->where('nl.nivel', $nivel);
            }

            if ($componente) {
                $query->where('ne.canal', $componente);
            }

            if ($busca) {
                $query->where(function ($q) use ($busca) {
                    $q->where('nl.mensagem', 'LIKE', "%{$busca}%")
                        ->orWhere('ne.email_destinatario', 'LIKE', "%{$busca}%")
                        ->orWhere('ne.telefone_destinatario', 'LIKE', "%{$busca}%")
                        ->orWhere('ne.titulo', 'LIKE', "%{$busca}%");
                });
            }

            $logs = $query->limit($limite)->get();

            // Processar logs para formato adequado
            $logsFormatados = $logs->map(function ($log) {
                $dados = json_decode($log->dados, true) ?? [];

                return [
                    'id' => $log->id,
                    'timestamp' => $log->created_at,
                    'nivel' => $log->nivel,
                    'componente' => $log->componente ?? 'sistema',
                    'mensagem' => $log->mensagem,
                    'contexto' => $dados,
                    'destinatario' => $log->email_destinatario ?? $log->telefone_destinatario ?? 'N/A',
                    'status' => $log->status ?? 'N/A',
                    'titulo' => $log->titulo ?? 'N/A',
                    'ip' => $dados['ip'] ?? '127.0.0.1',
                    'user_agent' => $dados['user_agent'] ?? 'Sistema'
                ];
            });

            return response()->json([
                'status' => 'success',
                'logs' => $logsFormatados,
                'total' => $logs->count(),
                'periodo' => $periodo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar logs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiEstatisticas(Request $request)
    {
        try {
            $periodo = $request->get('periodo', '1h');
            $dataInicio = $this->calcularDataInicio($periodo);

            // Estatísticas por nível
            $estatisticasNivel = DB::table('notificacao_logs')
                ->select('nivel', DB::raw('COUNT(*) as total'))
                ->where('created_at', '>=', $dataInicio)
                ->groupBy('nivel')
                ->get()
                ->pluck('total', 'nivel');

            // Estatísticas por canal/componente
            $estatisticasCanal = DB::table('notificacao_logs as nl')
                ->leftJoin('notificacao_enviadas as ne', 'nl.notificacao_id', '=', 'ne.id')
                ->select('ne.canal as componente', DB::raw('COUNT(*) as total'))
                ->where('nl.created_at', '>=', $dataInicio)
                ->whereNotNull('ne.canal')
                ->groupBy('ne.canal')
                ->get()
                ->pluck('total', 'componente');

            // Timeline dos últimos dados (últimas 24 horas por hora)
            $timeline = DB::table('notificacao_logs as nl')
                ->leftJoin('notificacao_enviadas as ne', 'nl.notificacao_id', '=', 'ne.id')
                ->select([
                    DB::raw('DATE_FORMAT(nl.created_at, "%H:00") as hora'),
                    'nl.nivel',
                    DB::raw('COUNT(*) as total')
                ])
                ->where('nl.created_at', '>=', Carbon::now()->subDay())
                ->groupBy(DB::raw('DATE_FORMAT(nl.created_at, "%H:00")'), 'nl.nivel')
                ->orderBy('hora')
                ->get();

            // Principais erros
            $principaisErros = DB::table('notificacao_logs as nl')
                ->leftJoin('notificacao_enviadas as ne', 'nl.notificacao_id', '=', 'ne.id')
                ->select([
                    'nl.mensagem as erro',
                    DB::raw('COUNT(*) as ocorrencias'),
                    DB::raw('MAX(nl.created_at) as ultima_ocorrencia')
                ])
                ->where('nl.nivel', 'error')
                ->where('nl.created_at', '>=', $dataInicio)
                ->groupBy('nl.mensagem')
                ->orderBy('ocorrencias', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'status' => 'success',
                'estatisticas' => [
                    'info' => $estatisticasNivel['info'] ?? 0,
                    'debug' => $estatisticasNivel['debug'] ?? 0,
                    'warning' => $estatisticasNivel['warning'] ?? 0,
                    'error' => $estatisticasNivel['error'] ?? 0,
                    'critical' => $estatisticasNivel['critical'] ?? 0
                ],
                'canais' => $estatisticasCanal,
                'timeline' => $timeline,
                'principais_erros' => $principaisErros
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar estatísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiDetalhes($id)
    {
        try {
            $log = DB::table('notificacao_logs as nl')
                ->leftJoin('notificacao_enviadas as ne', 'nl.notificacao_id', '=', 'ne.id')
                ->select([
                    'nl.*',
                    'ne.canal',
                    'ne.email_destinatario',
                    'ne.telefone_destinatario',
                    'ne.titulo',
                    'ne.mensagem as mensagem_notificacao',
                    'ne.status',
                    'ne.prioridade',
                    'ne.dados_processados',
                    'ne.dados_evento_origem'
                ])
                ->where('nl.id', $id)
                ->first();

            if (!$log) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Log não encontrado'
                ], 404);
            }

            // Processar dados
            $dados = json_decode($log->dados, true) ?? [];
            $dadosProcessados = json_decode($log->dados_processados, true) ?? [];
            $dadosEvento = json_decode($log->dados_evento_origem, true) ?? [];

            return response()->json([
                'status' => 'success',
                'log' => [
                    'id' => $log->id,
                    'timestamp' => $log->created_at,
                    'nivel' => $log->nivel,
                    'componente' => $log->canal ?? 'sistema',
                    'mensagem' => $log->mensagem,
                    'notificacao_id' => $log->notificacao_id,
                    'destinatario' => $log->email_destinatario ?? $log->telefone_destinatario ?? 'N/A',
                    'titulo' => $log->titulo,
                    'status' => $log->status,
                    'prioridade' => $log->prioridade,
                    'contexto' => $dados,
                    'dados_processados' => $dadosProcessados,
                    'dados_evento' => $dadosEvento,
                    'ip' => $dados['ip'] ?? '127.0.0.1',
                    'user_agent' => $dados['user_agent'] ?? 'Sistema'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar detalhes: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calcularDataInicio($periodo)
    {
        $agora = Carbon::now();

        switch ($periodo) {
            case '1h':
                return $agora->subHour();
            case '6h':
                return $agora->subHours(6);
            case '24h':
                return $agora->subDay();
            case '7d':
                return $agora->subDays(7);
            case '30d':
                return $agora->subDays(30);
            default:
                return $agora->subHour();
        }
    }
}
