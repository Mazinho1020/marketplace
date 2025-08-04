<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacaoConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresaId = 1; // Assumindo empresa ID 1

        // Inserir grupos de configuração
        $grupos = [
            [
                'empresa_id' => $empresaId,
                'codigo' => 'notifications',
                'nome' => 'Notificações',
                'descricao' => 'Sistema de notificações multi-aplicação',
                'icone_class' => 'fas fa-bell',
                'ordem' => 9,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'notification_apps',
                'nome' => 'Aplicações',
                'descricao' => 'Controle de aplicações ativas',
                'icone_class' => 'fas fa-mobile-alt',
                'ordem' => 1,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'notification_channels',
                'nome' => 'Canais',
                'descricao' => 'Configuração de canais por aplicação',
                'icone_class' => 'fas fa-broadcast-tower',
                'ordem' => 2,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'notification_automation',
                'nome' => 'Automação',
                'descricao' => 'Regras de eventos automáticos',
                'icone_class' => 'fas fa-robot',
                'ordem' => 3,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'codigo' => 'notification_behavior',
                'nome' => 'Comportamento',
                'descricao' => 'Regras gerais do sistema',
                'icone_class' => 'fas fa-cogs',
                'ordem' => 4,
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($grupos as $grupo) {
            DB::table('config_groups')->updateOrInsert(
                ['empresa_id' => $grupo['empresa_id'], 'codigo' => $grupo['codigo']],
                $grupo
            );
        }

        // Obter IDs dos grupos
        $grupoNotifications = DB::table('config_groups')->where('codigo', 'notifications')->where('empresa_id', $empresaId)->first();
        $grupoApps = DB::table('config_groups')->where('codigo', 'notification_apps')->where('empresa_id', $empresaId)->first();
        $grupoChannels = DB::table('config_groups')->where('codigo', 'notification_channels')->where('empresa_id', $empresaId)->first();
        $grupoAutomation = DB::table('config_groups')->where('codigo', 'notification_automation')->where('empresa_id', $empresaId)->first();
        $grupoBehavior = DB::table('config_groups')->where('codigo', 'notification_behavior')->where('empresa_id', $empresaId)->first();

        // Configurações
        $configuracoes = [
            // APLICAÇÕES (ATIVO/INATIVO)
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_cliente_enabled',
                'nome' => 'App Cliente',
                'descricao' => 'Ativar notificações para clientes',
                'tipo' => 'boolean',
                'grupo_id' => $grupoApps->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_empresa_enabled',
                'nome' => 'App Empresa',
                'descricao' => 'Ativar notificações para empresas',
                'tipo' => 'boolean',
                'grupo_id' => $grupoApps->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_admin_enabled',
                'nome' => 'App Admin',
                'descricao' => 'Ativar notificações administrativas',
                'tipo' => 'boolean',
                'grupo_id' => $grupoApps->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_entregador_enabled',
                'nome' => 'App Entregador',
                'descricao' => 'Ativar notificações para entregadores',
                'tipo' => 'boolean',
                'grupo_id' => $grupoApps->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'app_fidelidade_enabled',
                'nome' => 'App Fidelidade',
                'descricao' => 'Ativar programa de fidelidade',
                'tipo' => 'boolean',
                'grupo_id' => $grupoApps->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // CANAIS POR APLICAÇÃO
            [
                'empresa_id' => $empresaId,
                'chave' => 'channels_cliente',
                'nome' => 'Canais Cliente',
                'descricao' => 'Canais habilitados para clientes',
                'tipo' => 'json',
                'grupo_id' => $grupoChannels->id,
                'valor_padrao' => '["websocket", "push", "email", "in_app"]',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'channels_empresa',
                'nome' => 'Canais Empresa',
                'descricao' => 'Canais habilitados para empresas',
                'tipo' => 'json',
                'grupo_id' => $grupoChannels->id,
                'valor_padrao' => '["websocket", "push", "email", "in_app"]',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'channels_admin',
                'nome' => 'Canais Admin',
                'descricao' => 'Canais habilitados para admin',
                'tipo' => 'json',
                'grupo_id' => $grupoChannels->id,
                'valor_padrao' => '["websocket", "in_app"]',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'channels_entregador',
                'nome' => 'Canais Entregador',
                'descricao' => 'Canais habilitados para entregadores',
                'tipo' => 'json',
                'grupo_id' => $grupoChannels->id,
                'valor_padrao' => '["websocket", "push", "sms", "in_app"]',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'channels_fidelidade',
                'nome' => 'Canais Fidelidade',
                'descricao' => 'Canais habilitados para fidelidade',
                'tipo' => 'json',
                'grupo_id' => $grupoChannels->id,
                'valor_padrao' => '["websocket", "push", "email", "in_app"]',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // AUTOMAÇÃO
            [
                'empresa_id' => $empresaId,
                'chave' => 'auto_aniversario_enabled',
                'nome' => 'Aniversários',
                'descricao' => 'Ativar notificações automáticas de aniversário',
                'tipo' => 'boolean',
                'grupo_id' => $grupoAutomation->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 0,
                'editavel' => 1,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'auto_aniversario_time',
                'nome' => 'Horário Aniversário',
                'descricao' => 'Horário para enviar (HH:MM)',
                'tipo' => 'string',
                'grupo_id' => $grupoAutomation->id,
                'valor_padrao' => '09:00',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'auto_aniversario_apps',
                'nome' => 'Apps Aniversário',
                'descricao' => 'Aplicações que recebem notificação',
                'tipo' => 'json',
                'grupo_id' => $grupoAutomation->id,
                'valor_padrao' => '["cliente", "empresa", "fidelidade"]',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'auto_aniversario_bonus_points',
                'nome' => 'Pontos Bônus',
                'descricao' => 'Pontos de aniversário',
                'tipo' => 'integer',
                'grupo_id' => $grupoAutomation->id,
                'valor_padrao' => '100',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // COMPORTAMENTO GERAL
            [
                'empresa_id' => $empresaId,
                'chave' => 'notification_queue_enabled',
                'nome' => 'Usar Filas',
                'descricao' => 'Processar notificações em fila',
                'tipo' => 'boolean',
                'grupo_id' => $grupoBehavior->id,
                'valor_padrao' => 'true',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'notification_retry_attempts',
                'nome' => 'Tentativas',
                'descricao' => 'Número de tentativas em caso de erro',
                'tipo' => 'integer',
                'grupo_id' => $grupoBehavior->id,
                'valor_padrao' => '3',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'empresa_id' => $empresaId,
                'chave' => 'notification_rate_limit',
                'nome' => 'Rate Limit',
                'descricao' => 'Máximo de notificações por minuto',
                'tipo' => 'integer',
                'grupo_id' => $grupoBehavior->id,
                'valor_padrao' => '100',
                'obrigatorio' => 1,
                'editavel' => 1,
                'ordem' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($configuracoes as $config) {
            $configId = DB::table('config_definitions')->updateOrInsert(
                ['empresa_id' => $config['empresa_id'], 'chave' => $config['chave']],
                $config
            );

            // Criar valor padrão
            $configRecord = DB::table('config_definitions')->where('chave', $config['chave'])->where('empresa_id', $empresaId)->first();
            if ($configRecord) {
                DB::table('config_values')->updateOrInsert(
                    ['empresa_id' => $empresaId, 'config_id' => $configRecord->id],
                    [
                        'empresa_id' => $empresaId,
                        'config_id' => $configRecord->id,
                        'valor' => $config['valor_padrao'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
