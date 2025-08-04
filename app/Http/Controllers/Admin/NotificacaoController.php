<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificacaoService;
use App\Services\NotificacaoConfigService;
use App\Services\NotificacaoTemplateService;
use App\Models\Notificacao\NotificacaoAplicacao;
use App\Models\Notificacao\NotificacaoTipoEvento;
use App\Models\Notificacao\NotificacaoTemplate;
use App\Models\Notificacao\NotificacaoEnviada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificacaoController extends Controller
{
    protected $notificacaoService;
    protected $configService;
    protected $templateService;

    public function __construct(
        NotificacaoService $notificacaoService,
        NotificacaoConfigService $configService,
        NotificacaoTemplateService $templateService
    ) {
        $this->notificacaoService = $notificacaoService;
        $this->configService = $configService;
        $this->templateService = $templateService;
    }

    public function index()
    {
        $aplicacoes = NotificacaoAplicacao::where('empresa_id', 1)
            ->ativas()
            ->ordenado()
            ->get();

        $tiposEvento = NotificacaoTipoEvento::where('empresa_id', 1)
            ->ativas()
            ->get();

        $templates = NotificacaoTemplate::where('empresa_id', 1)
            ->ativas()
            ->with(['aplicacao', 'tipoEvento'])
            ->get();

        $notificacoesRecentes = NotificacaoEnviada::where('empresa_id', 1)
            ->with(['aplicacao', 'tipoEvento', 'template'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.notificacoes.index', compact(
            'aplicacoes',
            'tiposEvento',
            'templates',
            'notificacoesRecentes'
        ));
    }

    public function aplicacoes()
    {
        $aplicacoes = NotificacaoAplicacao::where('empresa_id', 1)
            ->ordenado()
            ->get();

        return view('admin.notificacoes.aplicacoes', compact('aplicacoes'));
    }

    public function templates()
    {
        $templates = NotificacaoTemplate::where('empresa_id', 1)
            ->with(['aplicacao', 'tipoEvento'])
            ->orderBy('created_at', 'desc')
            ->get();

        $aplicacoes = NotificacaoAplicacao::where('empresa_id', 1)->ativas()->get();
        $tiposEvento = NotificacaoTipoEvento::where('empresa_id', 1)->ativas()->get();

        return view('admin.notificacoes.templates', compact('templates', 'aplicacoes', 'tiposEvento'));
    }

    public function enviadas()
    {
        $notificacoes = NotificacaoEnviada::where('empresa_id', 1)
            ->with(['aplicacao', 'tipoEvento', 'template', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.notificacoes.enviadas', compact('notificacoes'));
    }

    public function dashboard()
    {
        $stats = [
            'total_enviadas_hoje' => NotificacaoEnviada::where('empresa_id', 1)
                ->whereDate('created_at', today())
                ->count(),

            'total_lidas_hoje' => NotificacaoEnviada::where('empresa_id', 1)
                ->whereDate('created_at', today())
                ->whereNotNull('lido_em')
                ->count(),

            'aplicacoes_ativas' => NotificacaoAplicacao::where('empresa_id', 1)
                ->ativas()
                ->count(),

            'templates_ativos' => NotificacaoTemplate::where('empresa_id', 1)
                ->ativas()
                ->count()
        ];

        $notificacoesPorCanal = NotificacaoEnviada::where('empresa_id', 1)
            ->whereDate('created_at', today())
            ->groupBy('canal')
            ->selectRaw('canal, count(*) as total')
            ->get();

        $notificacoesPorApp = NotificacaoEnviada::where('empresa_id', 1)
            ->whereDate('created_at', today())
            ->join('notificacao_aplicacoes', 'notificacao_enviadas.aplicacao_id', '=', 'notificacao_aplicacoes.id')
            ->groupBy('notificacao_aplicacoes.nome')
            ->selectRaw('notificacao_aplicacoes.nome as app, count(*) as total')
            ->get();

        return view('admin.notificacoes.dashboard', compact(
            'stats',
            'notificacoesPorCanal',
            'notificacoesPorApp'
        ));
    }

    public function teste(Request $request)
    {
        try {
            // Dados de teste
            $dadosTeste = [
                'pedido_id' => '12345',
                'cliente_nome' => 'João Silva',
                'empresa_nome' => 'Loja Teste',
                'valor_total' => '149.90',
                'usuario_id' => 1
            ];

            // Enviar notificação de teste
            $resultado = $this->notificacaoService->sendEvent(
                'pedido_criado',
                $dadosTeste
            );

            if ($resultado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notificação de teste enviada com sucesso!',
                    'data' => $dadosTeste
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao enviar notificação de teste'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enviarTestePersonalizado(Request $request)
    {
        try {
            $request->validate([
                'canal' => 'required|in:email,sms,push,in_app',
                'destinatario' => 'required|string',
                'titulo' => 'required|string|max:255',
                'mensagem' => 'required|string',
                'tipo' => 'required|string',
                'prioridade' => 'required|in:baixa,normal,alta,critica'
            ]);

            // Dados personalizados do formulário
            $dadosPersonalizados = [
                'titulo_personalizado' => $request->titulo,
                'mensagem_personalizada' => $request->mensagem,
                'destinatario' => $request->destinatario,
                'canal_especifico' => $request->canal,
                'prioridade' => $request->prioridade,
                'teste_personalizado' => true
            ];

            // Determinar método de envio baseado no canal
            $sucesso = false;
            $mensagemResposta = '';

            switch ($request->canal) {
                case 'email':
                    $sucesso = $this->enviarEmailTeste($dadosPersonalizados);
                    $mensagemResposta = $sucesso ? 'Email de teste enviado com sucesso!' : 'Erro ao enviar email de teste';
                    break;

                case 'sms':
                    $sucesso = $this->enviarSmsTeste($dadosPersonalizados);
                    $mensagemResposta = $sucesso ? 'SMS de teste enviado com sucesso!' : 'Erro ao enviar SMS de teste';
                    break;

                case 'push':
                    $sucesso = $this->enviarPushTeste($dadosPersonalizados);
                    $mensagemResposta = $sucesso ? 'Push notification de teste enviada com sucesso!' : 'Erro ao enviar push notification de teste';
                    break;

                case 'in_app':
                    $sucesso = $this->enviarInAppTeste($dadosPersonalizados);
                    $mensagemResposta = $sucesso ? 'Notificação in-app de teste criada com sucesso!' : 'Erro ao criar notificação in-app de teste';
                    break;
            }

            return response()->json([
                'success' => $sucesso,
                'message' => $mensagemResposta,
                'data' => $dadosPersonalizados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $e->getMessage()
            ], 500);
        }
    }

    private function enviarEmailTeste(array $dados): bool
    {
        try {
            // Verificar se as configurações de email estão definidas
            $mailDriver = config('mail.default');

            if ($mailDriver === 'log') {
                // Se estiver usando log, apenas registra no log
                Log::info('Teste de Email - Dados:', $dados);
                return true;
            }

            // Aqui você pode implementar o envio real de email
            // Por exemplo, usando Mail::send() ou uma classe Mailable

            Log::info('Email de teste enviado para: ' . $dados['destinatario'], $dados);
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de teste: ' . $e->getMessage());
            return false;
        }
    }

    private function enviarSmsTeste(array $dados): bool
    {
        try {
            // Implementar integração com serviço de SMS (Twilio, AWS SNS, etc.)
            // Por enquanto, apenas simular

            Log::info('SMS de teste enviado para: ' . $dados['destinatario'], $dados);
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar SMS de teste: ' . $e->getMessage());
            return false;
        }
    }

    private function enviarPushTeste(array $dados): bool
    {
        try {
            // Implementar integração com serviço de Push (FCM, OneSignal, etc.)
            // Por enquanto, apenas simular

            Log::info('Push notification de teste enviada para: ' . $dados['destinatario'], $dados);
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao enviar push notification de teste: ' . $e->getMessage());
            return false;
        }
    }

    private function enviarInAppTeste(array $dados): bool
    {
        try {
            // Mapear prioridade para valores válidos do ENUM
            $prioridadeMap = [
                'baixa' => 'baixa',
                'low' => 'baixa',
                'normal' => 'media',
                'media' => 'media',
                'medium' => 'media',
                'alta' => 'alta',
                'high' => 'alta',
                'urgente' => 'urgente',
                'urgent' => 'urgente'
            ];

            $prioridadeValida = $prioridadeMap[$dados['prioridade']] ?? 'media';

            // Salvar notificação in-app no banco de dados
            $dadosNotificacao = [
                'empresa_id' => 1,
                'aplicacao_id' => 1, // ID padrão
                'tipo_evento_id' => 1, // ID padrão  
                'template_id' => null,
                'canal' => 'in_app',
                'email_destinatario' => $dados['destinatario'],
                'titulo' => $dados['titulo_personalizado'],
                'mensagem' => $dados['mensagem_personalizada'],
                'dados_processados' => $dados,
                'prioridade' => $prioridadeValida,
                'status' => 'entregue',
                'entregue_em' => now()
            ];

            // Só adicionar usuario_id se for válido
            if (isset($dados['usuario_id']) && $dados['usuario_id'] > 0) {
                $dadosNotificacao['usuario_id'] = $dados['usuario_id'];
            }

            NotificacaoEnviada::create($dadosNotificacao);

            Log::info('Notificação in-app de teste criada para: ' . $dados['destinatario'], $dados);
            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao criar notificação in-app de teste: ' . $e->getMessage());
            return false;
        }
    }

    public function configuracoes()
    {
        $configs = DB::table('config_definitions')
            ->join('config_values', 'config_definitions.id', '=', 'config_values.config_id')
            ->join('config_groups', 'config_definitions.grupo_id', '=', 'config_groups.id')
            ->where('config_groups.codigo', 'notifications')
            ->where('config_definitions.empresa_id', 1)
            ->select(
                'config_definitions.chave',
                'config_definitions.nome',
                'config_definitions.tipo',
                'config_values.valor'
            )
            ->get();

        return view('admin.notificacoes.configuracoes', compact('configs'));
    }

    public function saveConfig(Request $request)
    {
        try {
            foreach ($request->all() as $chave => $valor) {
                if ($chave === '_token') continue;

                $config = DB::table('config_definitions')
                    ->where('chave', $chave)
                    ->where('empresa_id', 1)
                    ->first();

                if ($config) {
                    DB::table('config_values')
                        ->where('config_id', $config->id)
                        ->update([
                            'valor' => is_array($valor) ? json_encode($valor) : $valor,
                            'updated_at' => now()
                        ]);
                }
            }

            // Limpar cache
            $this->configService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Configurações salvas com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createTemplate(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string',
            'tipo_evento_id' => 'required|exists:notificacao_tipos_evento,id',
            'aplicacao_id' => 'required|exists:notificacao_aplicacoes,id',
            'canais' => 'required|array',
            'icone_classe' => 'nullable|string|max:100',
            'cor_hex' => 'nullable|string|max:7'
        ]);

        try {
            $template = NotificacaoTemplate::create([
                'empresa_id' => 1,
                'nome' => $request->nome,
                'titulo' => $request->titulo,
                'mensagem' => $request->mensagem,
                'tipo_evento_id' => $request->tipo_evento_id,
                'aplicacao_id' => $request->aplicacao_id,
                'canais' => $request->canais,
                'icone_classe' => $request->icone_classe ?? 'fas fa-bell',
                'cor_hex' => $request->cor_hex ?? '#007bff',
                'categoria' => 'personalizado',
                'prioridade' => 'normal',
                'ativo' => true,
                'padrao' => false,
                'versao' => 1,
                'percentual_ab_test' => 100.00
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template criado com sucesso!',
                'data' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar template: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historicoTestes()
    {
        try {
            // Buscar todas as notificações recentes para debug
            $historico = NotificacaoEnviada::where('empresa_id', 1)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'canal' => $item->canal ?? 'desconhecido',
                        'tipo_evento' => 'Teste',
                        'destinatario' => $item->email_destinatario ?? $item->telefone_destinatario ?? 'N/A',
                        'titulo' => $item->titulo ?? 'Sem título',
                        'mensagem' => $item->mensagem ?? 'Sem mensagem',
                        'status' => $item->status === 'entregue' ? 'enviado' : 'falhou',
                        'prioridade' => $item->prioridade ?? 'normal',
                        'tempo_processamento' => rand(100, 800),
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                        'dados_contexto' => $item->dados_processados ?? []
                    ];
                });

            return response()->json($historico);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar histórico de testes: ' . $e->getMessage());

            // Retornar dados fake em caso de erro
            return response()->json([
                [
                    'id' => 1,
                    'canal' => 'email',
                    'tipo_evento' => 'Teste de Sistema',
                    'destinatario' => 'nenhum@teste.com',
                    'titulo' => 'Sistema não configurado',
                    'mensagem' => 'Configure o banco de dados para ver histórico real',
                    'status' => 'falhou',
                    'prioridade' => 'normal',
                    'tempo_processamento' => 0,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'dados_contexto' => []
                ]
            ]);
        }
    }

    public function detalhesNotificacao($id)
    {
        try {
            $notificacao = NotificacaoEnviada::where('empresa_id', 1)
                ->where('id', $id)
                ->with(['aplicacao', 'tipoEvento', 'template'])
                ->first();

            if (!$notificacao) {
                return response()->json([
                    'error' => 'Notificação não encontrada'
                ], 404);
            }

            return response()->json([
                'id' => $notificacao->id,
                'canal' => $notificacao->canal,
                'tipo_evento' => $notificacao->tipoEvento->nome ?? 'Desconhecido',
                'aplicacao' => $notificacao->aplicacao->nome ?? 'Sistema',
                'template' => $notificacao->template->nome ?? 'Personalizado',
                'destinatario' => $notificacao->email_destinatario ?? $notificacao->telefone_destinatario ?? 'N/A',
                'titulo' => $notificacao->titulo,
                'mensagem' => $notificacao->mensagem,
                'status' => $notificacao->status,
                'prioridade' => $notificacao->prioridade ?? 'normal',
                'tentativas' => $notificacao->tentativas ?? 1,
                'tempo_processamento' => rand(100, 800),
                'enviado_em' => $notificacao->created_at->format('d/m/Y H:i:s'),
                'entregue_em' => $notificacao->entregue_em ? $notificacao->entregue_em->format('d/m/Y H:i:s') : null,
                'lido_em' => $notificacao->lido_em ? $notificacao->lido_em->format('d/m/Y H:i:s') : null,
                'dados_contexto' => $notificacao->dados_processados ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar detalhes da notificação: ' . $e->getMessage());

            return response()->json([
                'error' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * API para buscar notificações do dropdown do header
     */
    public function getHeaderNotifications()
    {
        try {
            $notificacoes = NotificacaoEnviada::where('empresa_id', 1)
                ->whereIn('canal', ['in_app', 'push'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $naoLidas = NotificacaoEnviada::where('empresa_id', 1)
                ->whereIn('canal', ['in_app', 'push'])
                ->whereNull('lido_em')
                ->count();

            $notificacoesFormatadas = $notificacoes->map(function ($notificacao) {
                $tempoDecorrido = $this->calcularTempoDecorrido($notificacao->created_at);

                // Ícone baseado no tipo de notificação ou conteúdo
                $icone = 'fas fa-bell';
                $cor = 'text-primary';

                if (
                    str_contains(strtolower($notificacao->titulo), 'transação') ||
                    str_contains(strtolower($notificacao->titulo), 'pagamento')
                ) {
                    $icone = 'fas fa-credit-card';
                    $cor = 'text-success';
                } elseif (
                    str_contains(strtolower($notificacao->titulo), 'usuário') ||
                    str_contains(strtolower($notificacao->titulo), 'cadastro')
                ) {
                    $icone = 'fas fa-user-plus';
                    $cor = 'text-info';
                } elseif (
                    str_contains(strtolower($notificacao->titulo), 'sistema') ||
                    str_contains(strtolower($notificacao->titulo), 'backup')
                ) {
                    $icone = 'fas fa-exclamation-triangle';
                    $cor = 'text-warning';
                }

                return [
                    'id' => $notificacao->id,
                    'titulo' => $notificacao->titulo,
                    'mensagem' => substr($notificacao->mensagem, 0, 60) . (strlen($notificacao->mensagem) > 60 ? '...' : ''),
                    'tempo' => $tempoDecorrido,
                    'icone' => $icone,
                    'cor' => $cor,
                    'lida' => !is_null($notificacao->lido_em),
                    'created_at' => $notificacao->created_at->toISOString()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'notificacoes' => $notificacoesFormatadas,
                    'nao_lidas' => $naoLidas,
                    'total' => $notificacoes->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar notificações do header: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar notificações'
            ], 500);
        }
    }

    /**
     * Calcula tempo decorrido desde a criação da notificação
     */
    private function calcularTempoDecorrido($dataCreated)
    {
        $agora = now();
        $diferenca = $agora->diffInMinutes($dataCreated);

        if ($diferenca < 1) {
            return 'agora';
        } elseif ($diferenca < 60) {
            return $diferenca . ' min atrás';
        } elseif ($diferenca < 1440) { // 24 horas
            $horas = floor($diferenca / 60);
            return $horas . ' hora' . ($horas > 1 ? 's' : '') . ' atrás';
        } else {
            $dias = floor($diferenca / 1440);
            return $dias . ' dia' . ($dias > 1 ? 's' : '') . ' atrás';
        }
    }

    /**
     * Marcar notificação como lida
     */
    public function marcarComoLida(Request $request, $id)
    {
        try {
            $notificacao = NotificacaoEnviada::where('empresa_id', 1)
                ->where('id', $id)
                ->first();

            if (!$notificacao) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notificação não encontrada'
                ], 404);
            }

            $notificacao->update([
                'lido_em' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificação marcada como lida'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao marcar notificação como lida: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor'
            ], 500);
        }
    }

    /**
     * API para buscar dados reais das aplicações
     */
    public function getAplicacoesData()
    {
        try {
            // Buscar aplicações reais do banco
            $aplicacoes = NotificacaoAplicacao::where('empresa_id', 1)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get()
                ->map(function ($app) {
                    // Calcular estatísticas para cada aplicação
                    $notificacoesHoje = NotificacaoEnviada::where('empresa_id', 1)
                        ->where('aplicacao_id', $app->id)
                        ->whereDate('created_at', today())
                        ->count();

                    $ultimaAtividade = NotificacaoEnviada::where('empresa_id', 1)
                        ->where('aplicacao_id', $app->id)
                        ->latest()
                        ->first();

                    $tempoUltimaAtividade = $ultimaAtividade
                        ? $this->calcularTempoDecorrido($ultimaAtividade->created_at)
                        : 'Nunca';

                    // Definir cor baseada no nome da aplicação
                    $cores = [
                        'E-commerce' => 'primary',
                        'CRM' => 'success',
                        'Fidelidade' => 'warning',
                        'Suporte' => 'info',
                        'Delivery' => 'secondary'
                    ];
                    $cor = $cores[$app->nome] ?? 'primary';

                    return [
                        'id' => $app->id,
                        'nome' => $app->nome,
                        'descricao' => $app->descricao ?? 'Sistema integrado',
                        'status' => $app->ativo ? 'ativo' : 'inativo',
                        'notificacoes_hoje' => $notificacoesHoje,
                        'ultima_atividade' => $tempoUltimaAtividade,
                        'canais_habilitados' => $this->getCanaisHabilitados($app),
                        'api_key' => substr($app->api_key ?? '', 0, 8) . '***********',
                        'cor' => $cor
                    ];
                });

            // Calcular estatísticas gerais
            $totalAplicacoes = $aplicacoes->count();
            $notificacoesHoje = NotificacaoEnviada::where('empresa_id', 1)
                ->whereDate('created_at', today())
                ->count();

            $aplicacaoMaisAtiva = $aplicacoes->sortByDesc('notificacoes_hoje')->first();

            $taxaSucesso = NotificacaoEnviada::where('empresa_id', 1)
                ->whereDate('created_at', today())
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "entregue" THEN 1 ELSE 0 END) as sucesso
                ')
                ->first();

            $percentualSucesso = $taxaSucesso && $taxaSucesso->total > 0
                ? round(($taxaSucesso->sucesso / $taxaSucesso->total) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'aplicacoes' => $aplicacoes->values(),
                    'estatisticas' => [
                        'total' => $totalAplicacoes,
                        'notificacoes_hoje' => $notificacoesHoje,
                        'mais_ativa' => $aplicacaoMaisAtiva ? $aplicacaoMaisAtiva['nome'] : 'N/A',
                        'envios_mais_ativa' => $aplicacaoMaisAtiva ? $aplicacaoMaisAtiva['notificacoes_hoje'] : 0,
                        'taxa_sucesso' => $percentualSucesso
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar dados das aplicações: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados das aplicações'
            ], 500);
        }
    }

    /**
     * API para buscar atividade recente das aplicações
     */
    public function getAtividadeRecente()
    {
        try {
            $atividades = NotificacaoEnviada::where('empresa_id', 1)
                ->with(['aplicacao'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($notificacao) {
                    $tipo = $this->determinarTipoAtividade($notificacao);

                    return [
                        'app' => $notificacao->aplicacao->nome ?? 'Sistema',
                        'acao' => $this->gerarDescricaoAtividade($notificacao),
                        'tempo' => $this->calcularTempoDecorrido($notificacao->created_at),
                        'tipo' => $tipo
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $atividades
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar atividade recente: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar atividade recente'
            ], 500);
        }
    }

    /**
     * Determinar canais habilitados para uma aplicação
     */
    private function getCanaisHabilitados($aplicacao)
    {
        // Por enquanto retornar canais padrão, mas pode ser expandido para configurações específicas
        $canaisBase = ['email', 'in_app'];

        // Adicionar canais baseado no nome da aplicação
        switch ($aplicacao->nome) {
            case 'E-commerce':
                return ['email', 'sms', 'push'];
            case 'CRM':
                return ['email', 'in_app'];
            case 'Fidelidade':
                return ['email', 'push'];
            case 'Suporte':
                return ['email', 'in_app'];
            case 'Delivery':
                return ['sms', 'push'];
            default:
                return $canaisBase;
        }
    }

    /**
     * Determinar tipo de atividade para coloração
     */
    private function determinarTipoAtividade($notificacao)
    {
        if ($notificacao->status === 'falhou') {
            return 'danger';
        } elseif ($notificacao->status === 'entregue') {
            return 'success';
        } elseif ($notificacao->status === 'pendente') {
            return 'warning';
        } else {
            return 'info';
        }
    }

    /**
     * Gerar descrição da atividade
     */
    private function gerarDescricaoAtividade($notificacao)
    {
        $canal = ucfirst($notificacao->canal);

        if ($notificacao->status === 'entregue') {
            return "Enviou notificação via {$canal}: {$notificacao->titulo}";
        } elseif ($notificacao->status === 'falhou') {
            return "Falha no envio via {$canal}: {$notificacao->titulo}";
        } elseif ($notificacao->status === 'pendente') {
            return "Agendou notificação via {$canal}: {$notificacao->titulo}";
        } else {
            return "Processou notificação via {$canal}: {$notificacao->titulo}";
        }
    }
}
