<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notificacao\NotificacaoAplicacao;
use App\Models\Notificacao\NotificacaoTipoEvento;
use App\Models\Notificacao\NotificacaoTemplate;

class NotificacaoSeeder extends Seeder
{
    public function run()
    {
        $this->seedAplicacoes();
        $this->seedTiposEvento();
        $this->seedTemplates();
        $this->seedConfigDefinitions();
    }

    protected function seedAplicacoes()
    {
        $aplicacoes = [
            [
                'codigo' => 'cliente',
                'nome' => 'App Cliente',
                'descricao' => 'Aplicativo para clientes do marketplace',
                'icone_classe' => 'fas fa-user',
                'cor_hex' => '#007bff',
                'ativo' => true,
                'ordem_exibicao' => 1
            ],
            [
                'codigo' => 'empresa',
                'nome' => 'App Empresa',
                'descricao' => 'Aplicativo para empresas vendedoras',
                'icone_classe' => 'fas fa-store',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'ordem_exibicao' => 2
            ],
            [
                'codigo' => 'entregador',
                'nome' => 'App Entregador',
                'descricao' => 'Aplicativo para entregadores',
                'icone_classe' => 'fas fa-shipping-fast',
                'cor_hex' => '#ffc107',
                'ativo' => true,
                'ordem_exibicao' => 3
            ],
            [
                'codigo' => 'admin',
                'nome' => 'Painel Admin',
                'descricao' => 'Painel administrativo do sistema',
                'icone_classe' => 'fas fa-cogs',
                'cor_hex' => '#dc3545',
                'ativo' => true,
                'ordem_exibicao' => 4
            ],
            [
                'codigo' => 'fidelidade',
                'nome' => 'Sistema Fidelidade',
                'descricao' => 'Sistema de programa de fidelidade',
                'icone_classe' => 'fas fa-gift',
                'cor_hex' => '#6f42c1',
                'ativo' => true,
                'ordem_exibicao' => 5
            ]
        ];

        foreach ($aplicacoes as $app) {
            NotificacaoAplicacao::updateOrCreate(
                ['codigo' => $app['codigo'], 'empresa_id' => 1],
                array_merge($app, ['empresa_id' => 1])
            );
        }
        echo "✓ Aplicações inseridas!\n";
    }

    protected function seedTiposEvento()
    {
        $eventos = [
            [
                'codigo' => 'pedido_criado',
                'nome' => 'Pedido Criado',
                'descricao' => 'Novo pedido foi criado no sistema',
                'categoria' => 'pedidos',
                'automatico' => false,
                'aplicacoes_padrao' => ['cliente', 'empresa', 'admin'],
                'variaveis_disponiveis' => [
                    'pedido_id', 'cliente_nome', 'empresa_nome', 'valor_total', 'itens_qtd'
                ]
            ],
            [
                'codigo' => 'pagamento_confirmado',
                'nome' => 'Pagamento Confirmado',
                'descricao' => 'Pagamento do pedido foi confirmado',
                'categoria' => 'pagamentos',
                'automatico' => false,
                'aplicacoes_padrao' => ['cliente', 'empresa', 'entregador'],
                'variaveis_disponiveis' => [
                    'pedido_id', 'valor_pago', 'forma_pagamento', 'cliente_nome'
                ]
            ],
            [
                'codigo' => 'pedido_entregue',
                'nome' => 'Pedido Entregue',
                'descricao' => 'Pedido foi entregue ao cliente',
                'categoria' => 'entregas',
                'automatico' => false,
                'aplicacoes_padrao' => ['cliente', 'empresa', 'fidelidade'],
                'variaveis_disponiveis' => [
                    'pedido_id', 'entregador_nome', 'data_entrega', 'pontos_ganhos'
                ]
            ],
            [
                'codigo' => 'cliente_aniversario',
                'nome' => 'Aniversário Cliente',
                'descricao' => 'Aniversário do cliente (automático)',
                'categoria' => 'campanhas',
                'automatico' => true,
                'agendamento_cron' => '0 9 * * *',
                'aplicacoes_padrao' => ['cliente', 'fidelidade'],
                'variaveis_disponiveis' => [
                    'cliente_nome', 'pontos_bonus', 'cupom_desconto'
                ]
            ],
            [
                'codigo' => 'cliente_inativo_15',
                'nome' => 'Cliente Inativo 15 dias',
                'descricao' => 'Cliente inativo há 15 dias (automático)',
                'categoria' => 'reativacao',
                'automatico' => true,
                'agendamento_cron' => '0 10 * * *',
                'aplicacoes_padrao' => ['cliente', 'empresa'],
                'variaveis_disponiveis' => [
                    'cliente_nome', 'dias_inativo', 'cupom_volta'
                ]
            ]
        ];

        foreach ($eventos as $evento) {
            NotificacaoTipoEvento::updateOrCreate(
                ['codigo' => $evento['codigo'], 'empresa_id' => 1],
                array_merge($evento, ['empresa_id' => 1])
            );
        }
        echo "✓ Tipos de evento inseridos!\n";
    }

    protected function seedTemplates()
    {
        $templates = [
            // PEDIDO CRIADO
            [
                'evento_codigo' => 'pedido_criado',
                'app_codigo' => 'cliente',
                'nome' => 'Pedido Confirmado - Cliente',
                'titulo' => '🛒 Pedido confirmado!',
                'mensagem' => 'Seu pedido #{pedido_id} foi confirmado pela {empresa_nome}. Total: R$ {valor_total}',
                'canais' => ['websocket', 'push', 'in_app'],
                'icone_classe' => 'fas fa-shopping-cart',
                'cor_hex' => '#28a745'
            ],
            [
                'evento_codigo' => 'pedido_criado',
                'app_codigo' => 'empresa',
                'nome' => 'Novo Pedido - Empresa',
                'titulo' => '🔔 Novo pedido recebido!',
                'mensagem' => 'Pedido #{pedido_id} de {cliente_nome} - R$ {valor_total} ({itens_qtd} itens)',
                'canais' => ['websocket', 'push', 'email', 'in_app'],
                'icone_classe' => 'fas fa-bell',
                'cor_hex' => '#007bff'
            ],

            // PAGAMENTO CONFIRMADO
            [
                'evento_codigo' => 'pagamento_confirmado',
                'app_codigo' => 'cliente',
                'nome' => 'Pagamento Aprovado - Cliente',
                'titulo' => '💰 Pagamento aprovado!',
                'mensagem' => 'Pagamento de R$ {valor_pago} confirmado. Seu pedido #{pedido_id} está sendo preparado!',
                'canais' => ['websocket', 'push', 'in_app'],
                'icone_classe' => 'fas fa-credit-card',
                'cor_hex' => '#28a745'
            ],
            [
                'evento_codigo' => 'pagamento_confirmado',
                'app_codigo' => 'entregador',
                'nome' => 'Pedido Liberado - Entregador',
                'titulo' => '🚚 Pedido liberado para entrega',
                'mensagem' => 'Pedido #{pedido_id} pago e liberado. Aguardando retirada.',
                'canais' => ['websocket', 'push', 'in_app'],
                'icone_classe' => 'fas fa-truck',
                'cor_hex' => '#ffc107'
            ],

            // ANIVERSÁRIO
            [
                'evento_codigo' => 'cliente_aniversario',
                'app_codigo' => 'cliente',
                'nome' => 'Feliz Aniversário',
                'titulo' => '🎉 Feliz Aniversário, {cliente_nome}!',
                'mensagem' => 'Parabéns! Ganhe {pontos_bonus} pontos especiais no seu aniversário!',
                'canais' => ['push', 'email', 'in_app'],
                'icone_classe' => 'fas fa-birthday-cake',
                'cor_hex' => '#e83e8c'
            ],

            // CLIENTE INATIVO
            [
                'evento_codigo' => 'cliente_inativo_15',
                'app_codigo' => 'cliente',
                'nome' => 'Sentimos sua falta',
                'titulo' => '😢 Sentimos sua falta!',
                'mensagem' => 'Há {dias_inativo} dias você não faz pedido. Veja as novidades! Use: {cupom_volta}',
                'canais' => ['push', 'email'],
                'icone_classe' => 'fas fa-heart-broken',
                'cor_hex' => '#fd7e14'
            ]
        ];

        foreach ($templates as $template) {
            $tipoEvento = NotificacaoTipoEvento::where('codigo', $template['evento_codigo'])
                ->where('empresa_id', 1)
                ->first();
                
            $aplicacao = NotificacaoAplicacao::where('codigo', $template['app_codigo'])
                ->where('empresa_id', 1)
                ->first();

            if ($tipoEvento && $aplicacao) {
                NotificacaoTemplate::updateOrCreate(
                    [
                        'tipo_evento_id' => $tipoEvento->id,
                        'aplicacao_id' => $aplicacao->id,
                        'empresa_id' => 1
                    ],
                    [
                        'nome' => $template['nome'],
                        'titulo' => $template['titulo'],
                        'mensagem' => $template['mensagem'],
                        'canais' => $template['canais'],
                        'icone_classe' => $template['icone_classe'],
                        'cor_hex' => $template['cor_hex'],
                        'padrao' => true,
                        'ativo' => true,
                        'empresa_id' => 1,
                        'categoria' => 'sistema'
                    ]
                );
            }
        }
        echo "✓ Templates inseridos!\n";
    }

    protected function seedConfigDefinitions()
    {
        $configs = [
            // Apps ativas
            ['chave' => 'app_cliente_enabled', 'nome' => 'App Cliente Ativo', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'app_empresa_enabled', 'nome' => 'App Empresa Ativo', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'app_entregador_enabled', 'nome' => 'App Entregador Ativo', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'app_admin_enabled', 'nome' => 'App Admin Ativo', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'app_fidelidade_enabled', 'nome' => 'App Fidelidade Ativo', 'tipo' => 'boolean', 'valor' => 'true'],

            // Canais por app
            ['chave' => 'channels_cliente', 'nome' => 'Canais Cliente', 'tipo' => 'json', 'valor' => '["websocket", "push", "in_app"]'],
            ['chave' => 'channels_empresa', 'nome' => 'Canais Empresa', 'tipo' => 'json', 'valor' => '["websocket", "push", "email", "in_app"]'],
            ['chave' => 'channels_entregador', 'nome' => 'Canais Entregador', 'tipo' => 'json', 'valor' => '["websocket", "push", "in_app"]'],
            ['chave' => 'channels_admin', 'nome' => 'Canais Admin', 'tipo' => 'json', 'valor' => '["websocket", "in_app"]'],
            ['chave' => 'channels_fidelidade', 'nome' => 'Canais Fidelidade', 'tipo' => 'json', 'valor' => '["websocket"]'],

            // Automações
            ['chave' => 'auto_cliente_aniversario_enabled', 'nome' => 'Automação Aniversário', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'auto_cliente_aniversario_time', 'nome' => 'Horário Aniversário', 'tipo' => 'string', 'valor' => '09:00'],
            ['chave' => 'auto_cliente_aniversario_bonus_points', 'nome' => 'Pontos Bônus Aniversário', 'tipo' => 'integer', 'valor' => '100'],

            ['chave' => 'auto_cliente_inativo_15_enabled', 'nome' => 'Automação Cliente Inativo 15d', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'auto_cliente_inativo_15_time', 'nome' => 'Horário Cliente Inativo', 'tipo' => 'string', 'valor' => '10:00'],

            // Comportamento
            ['chave' => 'notification_queue_enabled', 'nome' => 'Notificações em Fila', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'notification_retry_attempts', 'nome' => 'Tentativas de Retry', 'tipo' => 'integer', 'valor' => '3'],
            ['chave' => 'notification_rate_limit', 'nome' => 'Limite de Rate (por minuto)', 'tipo' => 'integer', 'valor' => '100'],
            ['chave' => 'notification_debug_enabled', 'nome' => 'Debug de Notificações', 'tipo' => 'boolean', 'valor' => 'false'],
        ];

        // Busca ou cria grupo de notificações
        $grupoExists = \DB::table('config_groups')
            ->where('codigo', 'notifications')
            ->where('empresa_id', 1)
            ->exists();

        if (!$grupoExists) {
            $grupoId = \DB::table('config_groups')->insertGetId([
                'codigo' => 'notifications',
                'nome' => 'Notificações',
                'descricao' => 'Configurações do sistema de notificações',
                'empresa_id' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $grupoId = \DB::table('config_groups')
                ->where('codigo', 'notifications')
                ->where('empresa_id', 1)
                ->value('id');
        }

        foreach ($configs as $config) {
            $existing = \DB::table('config_definitions')
                ->where('empresa_id', 1)
                ->where('chave', $config['chave'])
                ->first();

            if (!$existing) {
                $definitionId = \DB::table('config_definitions')->insertGetId([
                    'empresa_id' => 1,
                    'grupo_id' => $grupoId,
                    'chave' => $config['chave'],
                    'nome' => $config['nome'],
                    'tipo' => $config['tipo'],
                    'valor_padrao' => $config['valor'],
                    'obrigatorio' => true,
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                \DB::table('config_values')->insert([
                    'config_id' => $definitionId,
                    'valor' => $config['valor'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        echo "✓ Configurações inseridas!\n";
    }
}
            ['empresa_id' => 1, 'codigo' => 'cliente_aniversario', 'nome' => 'Aniversário do Cliente', 'descricao' => 'Notificação automática no aniversário', 'categoria' => 'usuario', 'automatico' => true, 'agendamento_cron' => '0 9 * * *', 'variaveis_disponiveis' => json_encode(['cliente_nome', 'pontos_bonus', 'ofertas_especiais']), 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            ['empresa_id' => 1, 'codigo' => 'cliente_inativo_15', 'nome' => 'Cliente Inativo 15 dias', 'descricao' => 'Cliente sem pedidos há 15 dias', 'categoria' => 'usuario', 'automatico' => true, 'agendamento_cron' => '0 10 * * *', 'variaveis_disponiveis' => json_encode(['cliente_nome', 'dias_inativo', 'data_ultimo_pedido', 'ofertas_especiais']), 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            ['empresa_id' => 1, 'codigo' => 'cliente_risco_30', 'nome' => 'Cliente de Risco 30 dias', 'descricao' => 'Cliente de risco (30+ dias sem pedidos)', 'categoria' => 'usuario', 'automatico' => true, 'agendamento_cron' => '0 14 * * *', 'variaveis_disponiveis' => json_encode(['cliente_nome', 'dias_inativo', 'data_ultimo_pedido', 'ofertas_retencao']), 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()]
        ]);
        echo "✓ Tipos de evento inseridos!\n";

        // 5. Buscar IDs para inserir templates
        $apps = DB::table('notificacao_aplicacoes')->pluck('id', 'codigo');
        $eventos = DB::table('notificacao_tipos_evento')->pluck('id', 'codigo');

        // 6. Inserir templates padrão
        $templates = [
            // PEDIDO CRIADO
            ['empresa_id' => 1, 'tipo_evento_id' => $eventos['pedido_criado'], 'aplicacao_id' => $apps['cliente'], 'nome' => 'Pedido Criado - Cliente', 'titulo' => '🛒 Pedido confirmado!', 'mensagem' => 'Seu pedido #{{pedido_id}} foi confirmado e está sendo preparado pela {{empresa_nome}}.', 'canais' => json_encode(['websocket', 'push', 'email', 'in_app']), 'prioridade' => 'media', 'icone_classe' => 'fas fa-shopping-cart', 'cor_hex' => '#28a745', 'ativo' => true, 'padrao' => true, 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            ['empresa_id' => 1, 'tipo_evento_id' => $eventos['pedido_criado'], 'aplicacao_id' => $apps['empresa'], 'nome' => 'Pedido Criado - Empresa', 'titulo' => '🔔 Novo pedido recebido!', 'mensagem' => 'Novo pedido #{{pedido_id}} de {{cliente_nome}} - Total: R$ {{pedido_total}}', 'canais' => json_encode(['websocket', 'push', 'email', 'in_app']), 'prioridade' => 'alta', 'icone_classe' => 'fas fa-bell', 'cor_hex' => '#007bff', 'ativo' => true, 'padrao' => true, 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            ['empresa_id' => 1, 'tipo_evento_id' => $eventos['pedido_criado'], 'aplicacao_id' => $apps['admin'], 'nome' => 'Pedido Criado - Admin', 'titulo' => '📊 Novo pedido no sistema', 'mensagem' => 'Pedido #{{pedido_id}} - {{empresa_nome}} - R$ {{pedido_total}}', 'canais' => json_encode(['websocket', 'in_app']), 'prioridade' => 'baixa', 'icone_classe' => 'fas fa-chart-line', 'cor_hex' => '#6c757d', 'ativo' => true, 'padrao' => true, 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            
            // PAGAMENTO CONFIRMADO
            ['empresa_id' => 1, 'tipo_evento_id' => $eventos['pagamento_confirmado'], 'aplicacao_id' => $apps['cliente'], 'nome' => 'Pagamento Confirmado - Cliente', 'titulo' => '💳 Pagamento aprovado!', 'mensagem' => 'Pagamento do pedido #{{pedido_id}} foi confirmado. Seu pedido será processado em breve.', 'canais' => json_encode(['websocket', 'push', 'email', 'in_app']), 'prioridade' => 'alta', 'icone_classe' => 'fas fa-credit-card', 'cor_hex' => '#28a745', 'ativo' => true, 'padrao' => true, 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            ['empresa_id' => 1, 'tipo_evento_id' => $eventos['pagamento_confirmado'], 'aplicacao_id' => $apps['empresa'], 'nome' => 'Pagamento Confirmado - Empresa', 'titulo' => '💰 Pagamento recebido', 'mensagem' => 'Pagamento confirmado para o pedido #{{pedido_id}} - R$ {{pedido_total}}', 'canais' => json_encode(['websocket', 'push', 'in_app']), 'prioridade' => 'alta', 'icone_classe' => 'fas fa-money-bill-wave', 'cor_hex' => '#28a745', 'ativo' => true, 'padrao' => true, 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            
            // ANIVERSÁRIO
            ['empresa_id' => 1, 'tipo_evento_id' => $eventos['cliente_aniversario'], 'aplicacao_id' => $apps['cliente'], 'nome' => 'Aniversário - Cliente', 'titulo' => '🎉 Feliz Aniversário, {{cliente_nome}}!', 'mensagem' => 'Parabéns! Como presente, você ganhou {{pontos_bonus}} pontos especiais!', 'canais' => json_encode(['push', 'email', 'in_app']), 'prioridade' => 'media', 'icone_classe' => 'fas fa-birthday-cake', 'cor_hex' => '#e83e8c', 'ativo' => true, 'padrao' => true, 'sync_status' => 'pending', 'created_at' => now(), 'updated_at' => now()]
        ];

        DB::table('notificacao_templates')->insert($templates);
        echo "✓ Templates inseridos!\n";

        echo "\n🎉 Sistema de notificação configurado com sucesso!\n";
        echo "📊 Resumo:\n";
        echo "   - " . count($apps) . " aplicações\n";
        echo "   - " . count($eventos) . " tipos de evento\n";
        echo "   - " . count($templates) . " templates\n";
    }
}
            [
                'empresa_id' => 1,
                'codigo' => 'cliente',
                'nome' => 'Cliente',
                'descricao' => 'Aplicação para clientes do marketplace',
                'icone_classe' => 'fas fa-user',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'ordem_exibicao' => 1,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'empresa',
                'nome' => 'Empresa',
                'descricao' => 'Aplicação para empresas vendedoras',
                'icone_classe' => 'fas fa-building',
                'cor_hex' => '#007bff',
                'ativo' => true,
                'ordem_exibicao' => 2,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'admin',
                'nome' => 'Administrador',
                'descricao' => 'Painel administrativo do marketplace',
                'icone_classe' => 'fas fa-user-shield',
                'cor_hex' => '#6c757d',
                'ativo' => true,
                'ordem_exibicao' => 3,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'entregador',
                'nome' => 'Entregador',
                'descricao' => 'Aplicação para entregadores',
                'icone_classe' => 'fas fa-truck',
                'cor_hex' => '#ffc107',
                'ativo' => true,
                'ordem_exibicao' => 4,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'fidelidade',
                'nome' => 'Programa de Fidelidade',
                'descricao' => 'Sistema de pontos e fidelidade',
                'icone_classe' => 'fas fa-heart',
                'cor_hex' => '#e83e8c',
                'ativo' => true,
                'ordem_exibicao' => 5,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($aplicacoes as $aplicacao) {
            DB::table('notificacao_aplicacoes')->insert($aplicacao);
        }

        // Inserir tipos de eventos padrão
        $tiposEvento = [
            [
                'empresa_id' => 1,
                'codigo' => 'pedido_criado',
                'nome' => 'Pedido Criado',
                'descricao' => 'Notificação quando um novo pedido é criado',
                'categoria' => 'pedido',
                'automatico' => false,
                'agendamento_cron' => null,
                'variaveis_disponiveis' => json_encode(['pedido_id', 'cliente_nome', 'empresa_nome', 'pedido_total', 'quantidade_itens']),
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'pagamento_confirmado',
                'nome' => 'Pagamento Confirmado',
                'descricao' => 'Notificação quando pagamento é aprovado',
                'categoria' => 'pagamento',
                'automatico' => false,
                'agendamento_cron' => null,
                'variaveis_disponiveis' => json_encode(['pedido_id', 'cliente_nome', 'empresa_nome', 'pedido_total', 'metodo_pagamento']),
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'pedido_entregue',
                'nome' => 'Pedido Entregue',
                'descricao' => 'Notificação quando pedido é entregue',
                'categoria' => 'pedido',
                'automatico' => false,
                'agendamento_cron' => null,
                'variaveis_disponiveis' => json_encode(['pedido_id', 'cliente_nome', 'endereco_entrega', 'horario_entrega']),
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'cliente_aniversario',
                'nome' => 'Aniversário do Cliente',
                'descricao' => 'Notificação automática no aniversário',
                'categoria' => 'usuario',
                'automatico' => true,
                'agendamento_cron' => '0 9 * * *',
                'variaveis_disponiveis' => json_encode(['cliente_nome', 'pontos_bonus', 'ofertas_especiais']),
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'cliente_inativo_15',
                'nome' => 'Cliente Inativo 15 dias',
                'descricao' => 'Cliente sem pedidos há 15 dias',
                'categoria' => 'usuario',
                'automatico' => true,
                'agendamento_cron' => '0 10 * * *',
                'variaveis_disponiveis' => json_encode(['cliente_nome', 'dias_inativo', 'data_ultimo_pedido', 'ofertas_especiais']),
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'cliente_risco_30',
                'nome' => 'Cliente de Risco 30 dias',
                'descricao' => 'Cliente de risco (30+ dias sem pedidos)',
                'categoria' => 'usuario',
                'automatico' => true,
                'agendamento_cron' => '0 14 * * *',
                'variaveis_disponiveis' => json_encode(['cliente_nome', 'dias_inativo', 'data_ultimo_pedido', 'ofertas_retencao']),
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tiposEvento as $tipoEvento) {
            DB::table('notificacao_tipos_evento')->insert($tipoEvento);
        }

        // Obter IDs das aplicações e tipos de evento para criar templates
        $clienteApp = DB::table('notificacao_aplicacoes')->where('codigo', 'cliente')->first();
        $empresaApp = DB::table('notificacao_aplicacoes')->where('codigo', 'empresa')->first();
        $adminApp = DB::table('notificacao_aplicacoes')->where('codigo', 'admin')->first();
        $entregadorApp = DB::table('notificacao_aplicacoes')->where('codigo', 'entregador')->first();
        $fidelidadeApp = DB::table('notificacao_aplicacoes')->where('codigo', 'fidelidade')->first();

        $pedidoCriado = DB::table('notificacao_tipos_evento')->where('codigo', 'pedido_criado')->first();
        $pagamentoConfirmado = DB::table('notificacao_tipos_evento')->where('codigo', 'pagamento_confirmado')->first();
        $clienteAniversario = DB::table('notificacao_tipos_evento')->where('codigo', 'cliente_aniversario')->first();

        // Inserir templates padrão
        $templates = [
            // PEDIDO CRIADO
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pedidoCriado->id,
                'aplicacao_id' => $clienteApp->id,
                'nome' => 'Pedido Criado - Cliente',
                'titulo' => '🛒 Pedido confirmado!',
                'mensagem' => 'Seu pedido #{{pedido_id}} foi confirmado e está sendo preparado pela {{empresa_nome}}.',
                'canais' => json_encode(['websocket', 'push', 'email', 'in_app']),
                'prioridade' => 'media',
                'icone_classe' => 'fas fa-shopping-cart',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pedidoCriado->id,
                'aplicacao_id' => $empresaApp->id,
                'nome' => 'Pedido Criado - Empresa',
                'titulo' => '🔔 Novo pedido recebido!',
                'mensagem' => 'Novo pedido #{{pedido_id}} de {{cliente_nome}} - Total: R$ {{pedido_total}}',
                'canais' => json_encode(['websocket', 'push', 'email', 'in_app']),
                'prioridade' => 'alta',
                'icone_classe' => 'fas fa-bell',
                'cor_hex' => '#007bff',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pedidoCriado->id,
                'aplicacao_id' => $adminApp->id,
                'nome' => 'Pedido Criado - Admin',
                'titulo' => '📊 Novo pedido no sistema',
                'mensagem' => 'Pedido #{{pedido_id}} - {{empresa_nome}} - R$ {{pedido_total}}',
                'canais' => json_encode(['websocket', 'in_app']),
                'prioridade' => 'baixa',
                'icone_classe' => 'fas fa-chart-line',
                'cor_hex' => '#6c757d',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // PAGAMENTO CONFIRMADO
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pagamentoConfirmado->id,
                'aplicacao_id' => $clienteApp->id,
                'nome' => 'Pagamento Confirmado - Cliente',
                'titulo' => '💳 Pagamento aprovado!',
                'mensagem' => 'Pagamento do pedido #{{pedido_id}} foi confirmado. Seu pedido será processado em breve.',
                'canais' => json_encode(['websocket', 'push', 'email', 'in_app']),
                'prioridade' => 'alta',
                'icone_classe' => 'fas fa-credit-card',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pagamentoConfirmado->id,
                'aplicacao_id' => $empresaApp->id,
                'nome' => 'Pagamento Confirmado - Empresa',
                'titulo' => '💰 Pagamento recebido',
                'mensagem' => 'Pagamento confirmado para o pedido #{{pedido_id}} - R$ {{pedido_total}}',
                'canais' => json_encode(['websocket', 'push', 'in_app']),
                'prioridade' => 'alta',
                'icone_classe' => 'fas fa-money-bill-wave',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pagamentoConfirmado->id,
                'aplicacao_id' => $entregadorApp->id,
                'nome' => 'Pagamento Confirmado - Entregador',
                'titulo' => '🚚 Pedido liberado para entrega',
                'mensagem' => 'Pedido #{{pedido_id}} pago e liberado - {{endereco_entrega}}',
                'canais' => json_encode(['websocket', 'push', 'in_app']),
                'prioridade' => 'media',
                'icone_classe' => 'fas fa-truck',
                'cor_hex' => '#ffc107',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // ANIVERSÁRIO
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $clienteAniversario->id,
                'aplicacao_id' => $clienteApp->id,
                'nome' => 'Aniversário - Cliente',
                'titulo' => '🎉 Feliz Aniversário, {{cliente_nome}}!',
                'mensagem' => 'Parabéns! Como presente, você ganhou {{pontos_bonus}} pontos especiais!',
                'canais' => json_encode(['push', 'email', 'in_app']),
                'prioridade' => 'media',
                'icone_classe' => 'fas fa-birthday-cake',
                'cor_hex' => '#e83e8c',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $clienteAniversario->id,
                'aplicacao_id' => $empresaApp->id,
                'nome' => 'Aniversário - Empresa',
                'titulo' => '🎂 Cliente aniversariante: {{cliente_nome}}',
                'mensagem' => 'Oportunidade de engajamento - cliente faz aniversário hoje',
                'canais' => json_encode(['in_app']),
                'prioridade' => 'baixa',
                'icone_classe' => 'fas fa-birthday-cake',
                'cor_hex' => '#e83e8c',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $clienteAniversario->id,
                'aplicacao_id' => $fidelidadeApp->id,
                'nome' => 'Aniversário - Fidelidade',
                'titulo' => '🎁 Bônus de aniversário aplicado',
                'mensagem' => 'Cliente {{cliente_nome}} recebeu {{pontos_bonus}} pontos de aniversário',
                'canais' => json_encode(['websocket', 'in_app']),
                'prioridade' => 'baixa',
                'icone_classe' => 'fas fa-gift',
                'cor_hex' => '#e83e8c',
                'ativo' => true,
                'padrao' => true,
                'sync_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notificacao_templates')->insert($template);
        }
    }
}
