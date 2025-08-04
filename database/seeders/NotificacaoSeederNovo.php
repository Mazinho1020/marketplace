<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacaoSeederNovo extends Seeder
{
    public function run()
    {
        echo "🚀 Iniciando seed do sistema de notificações...\n";

        $this->seedAplicacoes();
        $this->seedTiposEvento();
        $this->seedTemplates();
        $this->seedConfigDefinitions();

        echo "✅ Sistema de notificações configurado com sucesso!\n";
    }

    protected function seedAplicacoes()
    {
        echo "📱 Inserindo aplicações...\n";

        $aplicacoes = [
            [
                'empresa_id' => 1,
                'codigo' => 'cliente',
                'nome' => 'App Cliente',
                'descricao' => 'Aplicativo para clientes do marketplace',
                'icone_classe' => 'fas fa-user',
                'cor_hex' => '#007bff',
                'ativo' => true,
                'ordem_exibicao' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'empresa',
                'nome' => 'App Empresa',
                'descricao' => 'Aplicativo para empresas vendedoras',
                'icone_classe' => 'fas fa-store',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'ordem_exibicao' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'entregador',
                'nome' => 'App Entregador',
                'descricao' => 'Aplicativo para entregadores',
                'icone_classe' => 'fas fa-shipping-fast',
                'cor_hex' => '#ffc107',
                'ativo' => true,
                'ordem_exibicao' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'admin',
                'nome' => 'Painel Admin',
                'descricao' => 'Painel administrativo do sistema',
                'icone_classe' => 'fas fa-cogs',
                'cor_hex' => '#dc3545',
                'ativo' => true,
                'ordem_exibicao' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'fidelidade',
                'nome' => 'Sistema Fidelidade',
                'descricao' => 'Sistema de programa de fidelidade',
                'icone_classe' => 'fas fa-gift',
                'cor_hex' => '#6f42c1',
                'ativo' => true,
                'ordem_exibicao' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($aplicacoes as $app) {
            DB::table('notificacao_aplicacoes')->updateOrInsert(
                ['codigo' => $app['codigo'], 'empresa_id' => 1],
                $app
            );
        }

        echo "✓ Aplicações inseridas!\n";
    }

    protected function seedTiposEvento()
    {
        echo "📋 Inserindo tipos de evento...\n";

        $eventos = [
            [
                'empresa_id' => 1,
                'codigo' => 'pedido_criado',
                'nome' => 'Pedido Criado',
                'descricao' => 'Novo pedido foi criado no sistema',
                'categoria' => 'pedidos',
                'automatico' => false,
                'aplicacoes_padrao' => json_encode(['cliente', 'empresa', 'admin']),
                'variaveis_disponiveis' => json_encode([
                    'pedido_id',
                    'cliente_nome',
                    'empresa_nome',
                    'valor_total',
                    'itens_qtd'
                ]),
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'pagamento_confirmado',
                'nome' => 'Pagamento Confirmado',
                'descricao' => 'Pagamento do pedido foi confirmado',
                'categoria' => 'pagamentos',
                'automatico' => false,
                'aplicacoes_padrao' => json_encode(['cliente', 'empresa', 'entregador']),
                'variaveis_disponiveis' => json_encode([
                    'pedido_id',
                    'valor_pago',
                    'forma_pagamento',
                    'cliente_nome'
                ]),
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'pedido_entregue',
                'nome' => 'Pedido Entregue',
                'descricao' => 'Pedido foi entregue ao cliente',
                'categoria' => 'entregas',
                'automatico' => false,
                'aplicacoes_padrao' => json_encode(['cliente', 'empresa', 'fidelidade']),
                'variaveis_disponiveis' => json_encode([
                    'pedido_id',
                    'entregador_nome',
                    'data_entrega',
                    'pontos_ganhos'
                ]),
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'cliente_aniversario',
                'nome' => 'Aniversário Cliente',
                'descricao' => 'Aniversário do cliente (automático)',
                'categoria' => 'campanhas',
                'automatico' => true,
                'agendamento_cron' => '0 9 * * *',
                'aplicacoes_padrao' => json_encode(['cliente', 'fidelidade']),
                'variaveis_disponiveis' => json_encode([
                    'cliente_nome',
                    'pontos_bonus',
                    'cupom_desconto'
                ]),
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'cliente_inativo_15',
                'nome' => 'Cliente Inativo 15 dias',
                'descricao' => 'Cliente inativo há 15 dias (automático)',
                'categoria' => 'reativacao',
                'automatico' => true,
                'agendamento_cron' => '0 10 * * *',
                'aplicacoes_padrao' => json_encode(['cliente', 'empresa']),
                'variaveis_disponiveis' => json_encode([
                    'cliente_nome',
                    'dias_inativo',
                    'cupom_volta'
                ]),
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($eventos as $evento) {
            DB::table('notificacao_tipos_evento')->updateOrInsert(
                ['codigo' => $evento['codigo'], 'empresa_id' => 1],
                $evento
            );
        }

        echo "✓ Tipos de evento inseridos!\n";
    }

    protected function seedTemplates()
    {
        echo "📝 Inserindo templates...\n";

        // Buscar IDs das aplicações e eventos
        $apps = DB::table('notificacao_aplicacoes')
            ->where('empresa_id', 1)
            ->pluck('id', 'codigo');

        $eventos = DB::table('notificacao_tipos_evento')
            ->where('empresa_id', 1)
            ->pluck('id', 'codigo');

        $templates = [
            // PEDIDO CRIADO - CLIENTE
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $eventos['pedido_criado'],
                'aplicacao_id' => $apps['cliente'],
                'nome' => 'Pedido Confirmado - Cliente',
                'titulo' => '🛒 Pedido confirmado!',
                'mensagem' => 'Seu pedido #{pedido_id} foi confirmado pela {empresa_nome}. Total: R$ {valor_total}',
                'canais' => json_encode(['websocket', 'push', 'in_app']),
                'icone_classe' => 'fas fa-shopping-cart',
                'cor_hex' => '#28a745',
                'padrao' => true,
                'ativo' => true,
                'categoria' => 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // PEDIDO CRIADO - EMPRESA
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $eventos['pedido_criado'],
                'aplicacao_id' => $apps['empresa'],
                'nome' => 'Novo Pedido - Empresa',
                'titulo' => '🔔 Novo pedido recebido!',
                'mensagem' => 'Pedido #{pedido_id} de {cliente_nome} - R$ {valor_total} ({itens_qtd} itens)',
                'canais' => json_encode(['websocket', 'push', 'email', 'in_app']),
                'icone_classe' => 'fas fa-bell',
                'cor_hex' => '#007bff',
                'padrao' => true,
                'ativo' => true,
                'categoria' => 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // PAGAMENTO CONFIRMADO - CLIENTE
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $eventos['pagamento_confirmado'],
                'aplicacao_id' => $apps['cliente'],
                'nome' => 'Pagamento Aprovado - Cliente',
                'titulo' => '💰 Pagamento aprovado!',
                'mensagem' => 'Pagamento de R$ {valor_pago} confirmado. Seu pedido #{pedido_id} está sendo preparado!',
                'canais' => json_encode(['websocket', 'push', 'in_app']),
                'icone_classe' => 'fas fa-credit-card',
                'cor_hex' => '#28a745',
                'padrao' => true,
                'ativo' => true,
                'categoria' => 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // ANIVERSÁRIO - CLIENTE
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $eventos['cliente_aniversario'],
                'aplicacao_id' => $apps['cliente'],
                'nome' => 'Feliz Aniversário',
                'titulo' => '🎉 Feliz Aniversário, {cliente_nome}!',
                'mensagem' => 'Parabéns! Ganhe {pontos_bonus} pontos especiais no seu aniversário!',
                'canais' => json_encode(['push', 'email', 'in_app']),
                'icone_classe' => 'fas fa-birthday-cake',
                'cor_hex' => '#e83e8c',
                'padrao' => true,
                'ativo' => true,
                'categoria' => 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // CLIENTE INATIVO - CLIENTE
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $eventos['cliente_inativo_15'],
                'aplicacao_id' => $apps['cliente'],
                'nome' => 'Sentimos sua falta',
                'titulo' => '😢 Sentimos sua falta!',
                'mensagem' => 'Há {dias_inativo} dias você não faz pedido. Veja as novidades! Use: {cupom_volta}',
                'canais' => json_encode(['push', 'email']),
                'icone_classe' => 'fas fa-heart-broken',
                'cor_hex' => '#fd7e14',
                'padrao' => true,
                'ativo' => true,
                'categoria' => 'sistema',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($templates as $template) {
            DB::table('notificacao_templates')->updateOrInsert(
                [
                    'tipo_evento_id' => $template['tipo_evento_id'],
                    'aplicacao_id' => $template['aplicacao_id'],
                    'empresa_id' => 1
                ],
                $template
            );
        }

        echo "✓ Templates inseridos!\n";
    }

    protected function seedConfigDefinitions()
    {
        echo "⚙️ Inserindo configurações...\n";

        // Buscar ou criar grupo
        $grupo = DB::table('config_groups')
            ->where('codigo', 'notifications')
            ->where('empresa_id', 1)
            ->first();

        if (!$grupo) {
            $grupoId = DB::table('config_groups')->insertGetId([
                'codigo' => 'notifications',
                'nome' => 'Notificações',
                'descricao' => 'Configurações do sistema de notificações',
                'empresa_id' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $grupoId = $grupo->id;
        }

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

        foreach ($configs as $config) {
            $existing = DB::table('config_definitions')
                ->where('empresa_id', 1)
                ->where('chave', $config['chave'])
                ->first();

            if (!$existing) {
                $definitionId = DB::table('config_definitions')->insertGetId([
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

                DB::table('config_values')->insert([
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
