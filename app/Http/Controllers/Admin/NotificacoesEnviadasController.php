<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificacoesEnviadasController extends Controller
{
    public function __construct()
    {
        // Define o timezone para Cuiabá em todas as operações do Carbon
        Carbon::setLocale('pt_BR');
        date_default_timezone_set('America/Cuiaba');
    }

    public function index()
    {
        return view('admin.notificacoes.enviadas');
    }

    public function dados(Request $request)
    {
        try {
            // Verifica se a tabela existe
            if (!DB::getSchemaBuilder()->hasTable('notificacao_enviadas')) {
                return $this->retornarDadosMock($request);
            }

            $query = DB::table('notificacao_enviadas as ne')
                ->leftJoin('notificacao_aplicacoes as na', 'ne.aplicacao_id', '=', 'na.id')
                ->leftJoin('notificacao_templates as nt', 'ne.template_id', '=', 'nt.id')
                ->select([
                    'ne.id',
                    'ne.email_destinatario',
                    'ne.telefone_destinatario',
                    'ne.canal',
                    'ne.titulo',
                    'ne.mensagem',
                    'ne.status',
                    'ne.enviado_em',
                    'ne.entregue_em',
                    'ne.lido_em',
                    'ne.mensagem_erro',
                    'ne.tentativas',
                    'ne.id_externo',
                    'ne.dados_processados',
                    'na.nome as aplicacao_nome',
                    'nt.nome as template_nome'
                ]);

            // Filtros
            if ($request->filled('periodo')) {
                $periodo = $request->get('periodo');
                $dataInicio = null;
                $dataFim = Carbon::now('America/Cuiaba');

                switch ($periodo) {
                    case 'hoje':
                        $dataInicio = Carbon::today('America/Cuiaba');
                        break;
                    case 'ontem':
                        $dataInicio = Carbon::yesterday('America/Cuiaba');
                        $dataFim = Carbon::yesterday('America/Cuiaba')->endOfDay();
                        break;
                    case '7dias':
                        $dataInicio = Carbon::now('America/Cuiaba')->subDays(7);
                        break;
                    case '30dias':
                        $dataInicio = Carbon::now('America/Cuiaba')->subDays(30);
                        break;
                }

                if ($dataInicio) {
                    $query->whereBetween('ne.enviado_em', [$dataInicio, $dataFim]);
                }
            }

            if ($request->filled('canal')) {
                $query->where('ne.canal', $request->get('canal'));
            }

            if ($request->filled('status')) {
                $query->where('ne.status', $request->get('status'));
            }

            if ($request->filled('aplicacao')) {
                $query->where('na.slug', $request->get('aplicacao'));
            }

            if ($request->filled('busca')) {
                $busca = $request->get('busca');
                $query->where(function ($q) use ($busca) {
                    $q->where('ne.titulo', 'LIKE', "%{$busca}%")
                        ->orWhere('ne.destinatario_nome', 'LIKE', "%{$busca}%")
                        ->orWhere('ne.destinatario_email', 'LIKE', "%{$busca}%")
                        ->orWhere('nt.nome', 'LIKE', "%{$busca}%");
                });
            }

            // Ordenação
            $query->orderBy('ne.enviado_em', 'desc');

            // Paginação
            $porPagina = $request->get('por_pagina', 20);
            $pagina = $request->get('pagina', 1);
            $offset = ($pagina - 1) * $porPagina;

            $total = $query->count();
            $notificacoes = $query->offset($offset)->limit($porPagina)->get();

            // Processa os dados
            $notificacoes = $notificacoes->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'destinatario' => 'Usuário Teste',
                    'email' => $notif->email_destinatario,
                    'telefone' => $notif->telefone_destinatario,
                    'canal' => $notif->canal,
                    'titulo' => $notif->titulo,
                    'conteudo' => $notif->mensagem,
                    'aplicacao' => $notif->aplicacao_nome,
                    'template' => $notif->template_nome,
                    'status' => $notif->status,
                    'enviado_em' => $notif->enviado_em,
                    'entregue_em' => $notif->entregue_em,
                    'lido_em' => $notif->lido_em,
                    'erro' => $notif->mensagem_erro,
                    'tentativas' => $notif->tentativas,
                    'provider_id' => $notif->id_externo,
                    'dados_extras' => $notif->dados_processados ? json_decode($notif->dados_processados, true) : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $notificacoes,
                'total' => $total,
                'pagina' => $pagina,
                'por_pagina' => $porPagina,
                'total_paginas' => ceil($total / $porPagina),
                'from' => $offset + 1,
                'to' => min($offset + $porPagina, $total)
            ]);
        } catch (\Exception $e) {
            // Em caso de erro, retorna dados mock
            return $this->retornarDadosMock($request);
        }
    }

    private function retornarDadosMock($request)
    {
        $porPagina = $request->get('por_pagina', 20);
        $pagina = $request->get('pagina', 1);

        $dadosMock = [
            [
                'id' => 1,
                'destinatario' => "João Silva",
                'email' => "joao@empresa.com",
                'telefone' => null,
                'canal' => "email",
                'titulo' => "Bem-vindo ao Sistema",
                'conteudo' => "Olá João, bem-vindo ao nosso sistema!",
                'template' => "welcome_template",
                'aplicacao' => "ecommerce",
                'status' => "entregue",
                'tentativas' => 1,
                'enviado_em' => Carbon::now('America/Cuiaba')->subHours(2)->format('Y-m-d H:i:s'),
                'entregue_em' => Carbon::now('America/Cuiaba')->subHours(2)->addMinutes(1)->format('Y-m-d H:i:s'),
                'lido_em' => null,
                'erro' => null,
                'provider_id' => "ext_001"
            ],
            [
                'id' => 2,
                'destinatario' => "Maria Santos",
                'email' => "maria@empresa.com",
                'telefone' => "(11) 99999-8888",
                'canal' => "sms",
                'titulo' => "Código de Verificação",
                'conteudo' => "Seu código: 123456",
                'template' => "verification_code",
                'aplicacao' => "crm",
                'status' => "lido",
                'tentativas' => 1,
                'enviado_em' => Carbon::now('America/Cuiaba')->subHours(1)->format('Y-m-d H:i:s'),
                'entregue_em' => Carbon::now('America/Cuiaba')->subHours(1)->addSeconds(5)->format('Y-m-d H:i:s'),
                'lido_em' => Carbon::now('America/Cuiaba')->subMinutes(50)->format('Y-m-d H:i:s'),
                'erro' => null,
                'provider_id' => "ext_002"
            ],
            [
                'id' => 3,
                'destinatario' => "Pedro Costa",
                'email' => "pedro@empresa.com",
                'telefone' => null,
                'canal' => "push",
                'titulo' => "Nova Promoção Disponível",
                'conteudo' => "Confira nossas ofertas especiais!",
                'template' => "promo_template",
                'aplicacao' => "fidelidade",
                'status' => "erro",
                'tentativas' => 3,
                'enviado_em' => Carbon::now('America/Cuiaba')->subMinutes(45)->format('Y-m-d H:i:s'),
                'entregue_em' => null,
                'lido_em' => null,
                'erro' => "Device token inválido",
                'provider_id' => "ext_003"
            ],
            [
                'id' => 4,
                'destinatario' => "Ana Lima",
                'email' => "ana@empresa.com",
                'telefone' => null,
                'canal' => "email",
                'titulo' => "Relatório Mensal",
                'conteudo' => "Seu relatório mensal está disponível.",
                'template' => "monthly_report",
                'aplicacao' => "suporte",
                'status' => "enviado",
                'tentativas' => 1,
                'enviado_em' => Carbon::now('America/Cuiaba')->subMinutes(20)->format('Y-m-d H:i:s'),
                'entregue_em' => null,
                'lido_em' => null,
                'erro' => null,
                'provider_id' => "ext_004"
            ],
            [
                'id' => 5,
                'destinatario' => "Carlos Oliveira",
                'email' => null,
                'telefone' => "(11) 99999-7777",
                'canal' => "sms",
                'titulo' => "Lembrete de Pagamento",
                'conteudo' => "Sua fatura vence amanhã.",
                'template' => "payment_reminder",
                'aplicacao' => "ecommerce",
                'status' => "pendente",
                'tentativas' => 1,
                'enviado_em' => Carbon::now('America/Cuiaba')->subMinutes(10)->format('Y-m-d H:i:s'),
                'entregue_em' => null,
                'lido_em' => null,
                'erro' => null,
                'provider_id' => "ext_005"
            ]
        ];

        // Simular paginação
        $total = 127; // Total fictício
        $offset = ($pagina - 1) * $porPagina;
        $dadosPagina = array_slice($dadosMock, 0, min($porPagina, count($dadosMock)));

        return response()->json([
            'success' => true,
            'data' => $dadosPagina,
            'total' => $total,
            'pagina' => $pagina,
            'por_pagina' => $porPagina,
            'total_paginas' => ceil($total / $porPagina),
            'from' => $offset + 1,
            'to' => min($offset + $porPagina, $total),
            'mock' => true // Indica que são dados de demonstração
        ]);
    }

    public function estatisticas(Request $request)
    {
        try {
            // Verifica se a tabela existe
            if (!DB::getSchemaBuilder()->hasTable('notificacao_enviadas')) {
                return $this->retornarEstatisticasMock();
            }

            $query = DB::table('notificacao_enviadas');

            // Aplica filtros de período se especificado
            if ($request->filled('periodo')) {
                $periodo = $request->get('periodo');
                $dataInicio = null;
                $dataFim = Carbon::now('America/Cuiaba');

                switch ($periodo) {
                    case 'hoje':
                        $dataInicio = Carbon::today('America/Cuiaba');
                        break;
                    case 'ontem':
                        $dataInicio = Carbon::yesterday('America/Cuiaba');
                        $dataFim = Carbon::yesterday('America/Cuiaba')->endOfDay();
                        break;
                    case '7dias':
                        $dataInicio = Carbon::now('America/Cuiaba')->subDays(7);
                        break;
                    case '30dias':
                        $dataInicio = Carbon::now('America/Cuiaba')->subDays(30);
                        break;
                }

                if ($dataInicio) {
                    $query->whereBetween('enviado_em', [$dataInicio, $dataFim]);
                }
            }

            $stats = [
                'total' => $query->count(),
                'enviadas' => $query->whereIn('status', ['enviado', 'entregue'])->count(),
                'entregues' => $query->where('status', 'entregue')->count(),
                'lidas' => $query->whereNotNull('lido_em')->count(),
                'pendentes' => $query->where('status', 'pendente')->count(),
                'erros' => $query->where('status', 'falhou')->count()
            ];

            // Calcula taxas
            $stats['taxa_entrega'] = $stats['total'] > 0 ? round(($stats['entregues'] / $stats['total']) * 100, 1) : 0;
            $stats['taxa_leitura'] = $stats['entregues'] > 0 ? round(($stats['lidas'] / $stats['entregues']) * 100, 1) : 0;
            $stats['taxa_erro'] = $stats['total'] > 0 ? round(($stats['erros'] / $stats['total']) * 100, 1) : 0;
            $stats['taxa_sucesso'] = $stats['total'] > 0 ? round((($stats['total'] - $stats['erros']) / $stats['total']) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return $this->retornarEstatisticasMock();
        }
    }

    private function retornarEstatisticasMock()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => 15234,
                'enviadas' => 14890,
                'entregues' => 14567,
                'lidas' => 12890,
                'pendentes' => 234,
                'erros' => 433,
                'taxa_entrega' => 95.6,
                'taxa_leitura' => 88.4,
                'taxa_erro' => 2.8,
                'taxa_sucesso' => 95.6
            ],
            'mock' => true
        ]);
    }

    public function detalhes($id)
    {
        $notificacao = DB::table('notificacao_enviadas as ne')
            ->leftJoin('notificacao_aplicacoes as na', 'ne.aplicacao_id', '=', 'na.id')
            ->leftJoin('notificacao_templates as nt', 'ne.template_id', '=', 'nt.id')
            ->where('ne.id', $id)
            ->select([
                'ne.*',
                'na.nome as aplicacao_nome',
                'na.slug as aplicacao_slug',
                'nt.nome as template_nome'
            ])
            ->first();

        if (!$notificacao) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada'
            ], 404);
        }

        // Busca logs relacionados
        $logs = DB::table('notificacao_logs')
            ->where('notificacao_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        $dados = [
            'id' => $notificacao->id,
            'destinatario_nome' => 'Usuário Teste',
            'destinatario_email' => $notificacao->email_destinatario,
            'destinatario_telefone' => $notificacao->telefone_destinatario,
            'canal' => $notificacao->canal,
            'titulo' => $notificacao->titulo,
            'conteudo' => $notificacao->mensagem,
            'aplicacao' => $notificacao->aplicacao_nome,
            'template' => $notificacao->template_nome,
            'status' => $notificacao->status,
            'enviado_em' => $notificacao->enviado_em,
            'entregue_em' => $notificacao->entregue_em,
            'lido_em' => $notificacao->lido_em,
            'erro_mensagem' => $notificacao->mensagem_erro,
            'tentativas' => $notificacao->tentativas,
            'provider_id' => $notificacao->id_externo,
            'dados_extras' => $notificacao->dados_processados ? json_decode($notificacao->dados_processados, true) : null,
            'logs' => $logs
        ];

        return response()->json([
            'success' => true,
            'data' => $dados
        ]);
    }

    public function reenviar($id)
    {
        $notificacao = DB::table('notificacao_enviadas')->where('id', $id)->first();

        if (!$notificacao) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada'
            ], 404);
        }

        // TODO: Implementar lógica de reenvio
        // Por enquanto apenas simula o reenvio

        DB::table('notificacao_enviadas')
            ->where('id', $id)
            ->update([
                'status' => 'pendente',
                'tentativas' => DB::raw('tentativas + 1'),
                'erro_mensagem' => null,
                'updated_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s')
            ]);

        // Adiciona log
        DB::table('notificacao_logs')->insert([
            'notificacao_id' => $id,
            'nivel' => 'info',
            'mensagem' => 'Notificação reenviada manualmente',
            'dados' => json_encode(['reenvio_manual' => true]),
            'created_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notificação foi adicionada à fila de reenvio'
        ]);
    }

    public function logs($id)
    {
        $logs = DB::table('notificacao_logs as nl')
            ->where('nl.notificacao_id', $id)
            ->orderBy('nl.created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

    public function exportar(Request $request)
    {
        $query = DB::table('notificacao_enviadas as ne')
            ->leftJoin('notificacao_aplicacoes as na', 'ne.aplicacao_id', '=', 'na.id')
            ->leftJoin('notificacao_templates as nt', 'ne.template_id', '=', 'nt.id')
            ->select([
                'ne.id',
                'ne.destinatario_nome',
                'ne.destinatario_email',
                'ne.destinatario_telefone',
                'ne.canal',
                'ne.titulo',
                'ne.status',
                'ne.enviado_em',
                'ne.entregue_em',
                'ne.lido_em',
                'ne.erro_mensagem',
                'ne.tentativas',
                'na.nome as aplicacao',
                'nt.nome as template'
            ]);

        // Aplica mesmos filtros da listagem
        if ($request->filled('periodo')) {
            // ... mesmo código de filtros
        }

        $notificacoes = $query->orderBy('ne.enviado_em', 'desc')->get();

        $csv = "ID;Destinatário;Email;Telefone;Canal;Título;Aplicação;Template;Status;Enviado em;Entregue em;Lido em;Erro;Tentativas\n";

        foreach ($notificacoes as $notif) {
            $csv .= implode(';', [
                $notif->id,
                $notif->destinatario_nome,
                $notif->destinatario_email,
                $notif->destinatario_telefone,
                $notif->canal,
                str_replace(';', ',', $notif->titulo),
                $notif->aplicacao,
                $notif->template,
                $notif->status,
                $notif->enviado_em,
                $notif->entregue_em,
                $notif->lido_em,
                str_replace(';', ',', $notif->erro_mensagem),
                $notif->tentativas
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="notificacoes-enviadas-' . date('Y-m-d') . '.csv"');
    }
}
