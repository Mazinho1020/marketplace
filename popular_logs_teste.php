<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICANDO E POPULANDO LOGS ===\n";

// Verificar logs existentes
$totalLogs = DB::table('notificacao_logs')->count();
echo "Total de logs existentes: $totalLogs\n";

if ($totalLogs == 0) {
    echo "Criando dados de teste para demonstração...\n";

    // Buscar algumas notificações existentes para associar logs
    $notificacoes = DB::table('notificacao_enviadas')->limit(5)->get(['id', 'canal', 'status']);

    if ($notificacoes->count() > 0) {
        foreach ($notificacoes as $notif) {
            // Log de sucesso
            DB::table('notificacao_logs')->insert([
                'notificacao_id' => $notif->id,
                'nivel' => 'info',
                'mensagem' => "Notificação {$notif->canal} processada com sucesso",
                'dados' => json_encode([
                    'canal' => $notif->canal,
                    'status' => $notif->status,
                    'ip' => '192.168.1.' . rand(100, 200),
                    'user_agent' => 'NotificationService/2.0',
                    'tempo_processamento' => rand(100, 500) . 'ms'
                ]),
                'created_at' => now()->subMinutes(rand(1, 120)),
                'updated_at' => now()
            ]);

            // Alguns logs de debug
            if (rand(1, 3) == 1) {
                DB::table('notificacao_logs')->insert([
                    'notificacao_id' => $notif->id,
                    'nivel' => 'debug',
                    'mensagem' => "Preparando template para {$notif->canal}",
                    'dados' => json_encode([
                        'template_loaded' => true,
                        'variables_count' => rand(3, 8),
                        'ip' => '192.168.1.' . rand(100, 200),
                        'user_agent' => 'TemplateEngine/1.5'
                    ]),
                    'created_at' => now()->subMinutes(rand(1, 120)),
                    'updated_at' => now()
                ]);
            }

            // Alguns warnings
            if (rand(1, 4) == 1) {
                DB::table('notificacao_logs')->insert([
                    'notificacao_id' => $notif->id,
                    'nivel' => 'warning',
                    'mensagem' => "Alta latência detectada no gateway {$notif->canal}",
                    'dados' => json_encode([
                        'latencia' => rand(2000, 5000) . 'ms',
                        'limite_recomendado' => '1000ms',
                        'ip' => '192.168.1.' . rand(100, 200),
                        'user_agent' => 'GatewayMonitor/1.0'
                    ]),
                    'created_at' => now()->subMinutes(rand(1, 120)),
                    'updated_at' => now()
                ]);
            }
        }

        // Alguns erros de exemplo
        $erros = [
            'Token de push notification expirado',
            'Email inválido detectado',
            'Gateway SMS temporariamente indisponível',
            'Limite de rate limite atingido',
            'Template não encontrado'
        ];

        foreach ($erros as $erro) {
            if (rand(1, 3) == 1) {
                DB::table('notificacao_logs')->insert([
                    'notificacao_id' => $notificacoes->random()->id,
                    'nivel' => 'error',
                    'mensagem' => $erro,
                    'dados' => json_encode([
                        'error_code' => rand(400, 500),
                        'retry_attempts' => rand(1, 3),
                        'ip' => '192.168.1.' . rand(100, 200),
                        'user_agent' => 'ErrorHandler/1.0'
                    ]),
                    'created_at' => now()->subMinutes(rand(1, 180)),
                    'updated_at' => now()
                ]);
            }
        }

        // Log crítico ocasional
        if (rand(1, 10) == 1) {
            DB::table('notificacao_logs')->insert([
                'notificacao_id' => null,
                'nivel' => 'critical',
                'mensagem' => 'Falha crítica no sistema de notificações',
                'dados' => json_encode([
                    'system_status' => 'degraded',
                    'affected_services' => ['email', 'sms'],
                    'ip' => '192.168.1.1',
                    'user_agent' => 'SystemMonitor/1.0'
                ]),
                'created_at' => now()->subHours(rand(1, 6)),
                'updated_at' => now()
            ]);
        }

        echo "✅ Dados de teste criados com sucesso!\n";
    }
}

// Estatísticas finais
$estatisticas = DB::table('notificacao_logs')
    ->select('nivel', DB::raw('COUNT(*) as total'))
    ->groupBy('nivel')
    ->get();

echo "\n📊 Estatísticas de logs:\n";
foreach ($estatisticas as $stat) {
    echo "- {$stat->nivel}: {$stat->total}\n";
}

$totalFinal = DB::table('notificacao_logs')->count();
echo "\nTotal de logs no banco: $totalFinal\n";
echo "✅ Sistema de logs pronto para uso!\n";
