<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacaoCompleteSeeder extends Seeder
{
    public function run(): void
    {
        echo "Inserindo dados do sistema de notificação...\n";

        // 1. Criar aplicações
        $this->createAplicacoes();

        // 2. Criar tipos de evento
        $this->createTiposEvento();

        // 3. Criar templates
        $this->createTemplates();

        // 4. Criar configurações
        $this->createConfigs();

        echo "✅ Sistema de notificações configurado com sucesso!\n";
    }

    protected function createAplicacoes()
    {
        $aplicacoes = [
            [
                'empresa_id' => 1,
                'codigo' => 'cliente',
                'nome' => 'App Cliente',
                'descricao' => 'Aplicativo para clientes',
                'icone_classe' => 'fas fa-user',
                'cor_hex' => '#007bff',
                'ativo' => true,
                'ordem_exibicao' => 1
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'empresa',
                'nome' => 'App Empresa',
                'descricao' => 'Aplicativo para empresas',
                'icone_classe' => 'fas fa-store',
                'cor_hex' => '#28a745',
                'ativo' => true,
                'ordem_exibicao' => 2
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'admin',
                'nome' => 'Painel Admin',
                'descricao' => 'Painel administrativo',
                'icone_classe' => 'fas fa-cogs',
                'cor_hex' => '#dc3545',
                'ativo' => true,
                'ordem_exibicao' => 3
            ]
        ];

        foreach ($aplicacoes as $app) {
            DB::table('notificacao_aplicacoes')
                ->updateOrInsert(
                    ['codigo' => $app['codigo'], 'empresa_id' => $app['empresa_id']],
                    array_merge($app, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
        }

        echo "✓ Aplicações criadas\n";
    }

    protected function createTiposEvento()
    {
        $eventos = [
            [
                'empresa_id' => 1,
                'codigo' => 'pedido_criado',
                'nome' => 'Pedido Criado',
                'descricao' => 'Novo pedido criado',
                'categoria' => 'pedidos',
                'automatico' => false,
                'variaveis_disponiveis' => json_encode(['pedido_id', 'cliente_nome', 'valor_total'])
            ],
            [
                'empresa_id' => 1,
                'codigo' => 'pagamento_confirmado',
                'nome' => 'Pagamento Confirmado',
                'descricao' => 'Pagamento aprovado',
                'categoria' => 'pagamentos',
                'automatico' => false,
                'variaveis_disponiveis' => json_encode(['pedido_id', 'valor_pago'])
            ]
        ];

        foreach ($eventos as $evento) {
            DB::table('notificacao_tipos_evento')
                ->updateOrInsert(
                    ['codigo' => $evento['codigo'], 'empresa_id' => $evento['empresa_id']],
                    array_merge($evento, [
                        'ativo' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
        }

        echo "✓ Tipos de evento criados\n";
    }

    protected function createTemplates()
    {
        // Buscar IDs das aplicações e eventos
        $clienteApp = DB::table('notificacao_aplicacoes')
            ->where('codigo', 'cliente')
            ->where('empresa_id', 1)
            ->first();

        $empresaApp = DB::table('notificacao_aplicacoes')
            ->where('codigo', 'empresa')
            ->where('empresa_id', 1)
            ->first();

        $pedidoEvento = DB::table('notificacao_tipos_evento')
            ->where('codigo', 'pedido_criado')
            ->where('empresa_id', 1)
            ->first();

        $pagamentoEvento = DB::table('notificacao_tipos_evento')
            ->where('codigo', 'pagamento_confirmado')
            ->where('empresa_id', 1)
            ->first();

        if (!$clienteApp || !$empresaApp || !$pedidoEvento || !$pagamentoEvento) {
            echo "⚠️ Aplicações ou eventos não encontrados para criar templates\n";
            return;
        }

        $templates = [
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pedidoEvento->id,
                'aplicacao_id' => $clienteApp->id,
                'nome' => 'Pedido Confirmado - Cliente',
                'titulo' => '🛒 Pedido confirmado!',
                'mensagem' => 'Seu pedido #{pedido_id} foi confirmado',
                'canais' => json_encode(['websocket', 'push', 'in_app']),
                'icone_classe' => 'fas fa-shopping-cart',
                'cor_hex' => '#28a745',
                'padrao' => true,
                'ativo' => true
            ],
            [
                'empresa_id' => 1,
                'tipo_evento_id' => $pedidoEvento->id,
                'aplicacao_id' => $empresaApp->id,
                'nome' => 'Novo Pedido - Empresa',
                'titulo' => '🔔 Novo pedido!',
                'mensagem' => 'Pedido #{pedido_id} de {cliente_nome}',
                'canais' => json_encode(['websocket', 'push', 'email']),
                'icone_classe' => 'fas fa-bell',
                'cor_hex' => '#007bff',
                'padrao' => true,
                'ativo' => true
            ]
        ];

        foreach ($templates as $template) {
            DB::table('notificacao_templates')
                ->updateOrInsert(
                    [
                        'tipo_evento_id' => $template['tipo_evento_id'],
                        'aplicacao_id' => $template['aplicacao_id'],
                        'empresa_id' => $template['empresa_id']
                    ],
                    array_merge($template, [
                        'categoria' => 'sistema',
                        'prioridade' => 'normal',
                        'versao' => 1,
                        'percentual_ab_test' => 100.00,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
        }

        echo "✓ Templates criados\n";
    }

    protected function createConfigs()
    {
        // Criar grupo de configurações
        $grupoId = DB::table('config_groups')
            ->updateOrInsert(
                ['codigo' => 'notifications', 'empresa_id' => 1],
                [
                    'codigo' => 'notifications',
                    'nome' => 'Notificações',
                    'descricao' => 'Configurações do sistema de notificações',
                    'empresa_id' => 1,
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

        if (!$grupoId) {
            $grupoId = DB::table('config_groups')
                ->where('codigo', 'notifications')
                ->where('empresa_id', 1)
                ->value('id');
        }

        $configs = [
            ['chave' => 'app_cliente_enabled', 'nome' => 'App Cliente Ativo', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'app_empresa_enabled', 'nome' => 'App Empresa Ativo', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'channels_cliente', 'nome' => 'Canais Cliente', 'tipo' => 'json', 'valor' => '["websocket", "push", "in_app"]'],
            ['chave' => 'channels_empresa', 'nome' => 'Canais Empresa', 'tipo' => 'json', 'valor' => '["websocket", "push", "email"]'],
            ['chave' => 'notification_queue_enabled', 'nome' => 'Fila Ativa', 'tipo' => 'boolean', 'valor' => 'true'],
            ['chave' => 'notification_rate_limit', 'nome' => 'Rate Limit', 'tipo' => 'integer', 'valor' => '100']
        ];

        foreach ($configs as $config) {
            // Verificar se já existe
            $existing = DB::table('config_definitions')
                ->where('chave', $config['chave'])
                ->where('empresa_id', 1)
                ->first();

            if (!$existing) {
                $definitionId = DB::table('config_definitions')->insertGetId([
                    'empresa_id' => 1,
                    'grupo_id' => $grupoId,
                    'chave' => $config['chave'],
                    'nome' => $config['nome'],
                    'tipo' => $config['tipo'],
                    'valor_padrao' => $config['valor'],
                    'obrigatorio' => false,
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

        echo "✓ Configurações criadas\n";
    }
}
