<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ADICIONANDO MAIS DADOS DE DEMONSTRAÇÃO ===\n";

// Buscar notificações push que criamos
$pushNotifications = DB::table('notificacao_enviadas')->where('canal', 'push')->get();

// Adicionar logs variados para demonstração
$logsParaAdicionar = [
    // Push notification logs
    [
        'notificacao_id' => $pushNotifications->first()->id ?? null,
        'nivel' => 'info',
        'mensagem' => 'Push notification enviada com sucesso',
        'dados' => json_encode([
            'canal' => 'push',
            'token' => 'fcm_token_abc123',
            'response_time' => '234ms',
            'ip' => '192.168.1.200',
            'user_agent' => 'FCMService/1.0'
        ]),
        'created_at' => now()->subMinutes(5),
        'updated_at' => now()
    ],
    [
        'notificacao_id' => $pushNotifications->last()->id ?? null,
        'nivel' => 'error',
        'mensagem' => 'Push notification falhou - Token inválido',
        'dados' => json_encode([
            'canal' => 'push',
            'error_code' => 'InvalidRegistration',
            'token' => 'invalid_token_***',
            'ip' => '192.168.1.201',
            'user_agent' => 'FCMService/1.0'
        ]),
        'created_at' => now()->subMinutes(10),
        'updated_at' => now()
    ],
    // SMS logs
    [
        'notificacao_id' => null,
        'nivel' => 'warning',
        'mensagem' => 'Gateway SMS com alta latência detectada',
        'dados' => json_encode([
            'gateway' => 'twilio',
            'latencia' => '4500ms',
            'limite_recomendado' => '1000ms',
            'ip' => '192.168.1.202',
            'user_agent' => 'SMSGatewayMonitor/2.0'
        ]),
        'created_at' => now()->subMinutes(15),
        'updated_at' => now()
    ],
    // Email logs
    [
        'notificacao_id' => null,
        'nivel' => 'debug',
        'mensagem' => 'Template de email carregado e processado',
        'dados' => json_encode([
            'template' => 'welcome-email',
            'variables_count' => 5,
            'processing_time' => '45ms',
            'ip' => '192.168.1.203',
            'user_agent' => 'TemplateEngine/3.1'
        ]),
        'created_at' => now()->subMinutes(20),
        'updated_at' => now()
    ],
    // Sistema logs
    [
        'notificacao_id' => null,
        'nivel' => 'critical',
        'mensagem' => 'Sistema de filas sobrecarregado',
        'dados' => json_encode([
            'queue_size' => 1500,
            'max_recommended' => 1000,
            'processing_rate' => '50/min',
            'ip' => '192.168.1.1',
            'user_agent' => 'QueueMonitor/1.0'
        ]),
        'created_at' => now()->subHour(),
        'updated_at' => now()
    ],
    // API logs
    [
        'notificacao_id' => null,
        'nivel' => 'info',
        'mensagem' => 'API endpoint /notifications acessado',
        'dados' => json_encode([
            'endpoint' => '/api/notifications',
            'method' => 'POST',
            'response_code' => 200,
            'response_time' => '123ms',
            'ip' => '203.0.113.45',
            'user_agent' => 'MobileApp/2.1.0'
        ]),
        'created_at' => now()->subMinutes(2),
        'updated_at' => now()
    ],
    // Database logs
    [
        'notificacao_id' => null,
        'nivel' => 'warning',
        'mensagem' => 'Query lenta detectada na tabela notificacao_enviadas',
        'dados' => json_encode([
            'query_time' => '2.5s',
            'affected_rows' => 25000,
            'query_type' => 'SELECT',
            'ip' => '192.168.1.10',
            'user_agent' => 'DatabaseMonitor/1.0'
        ]),
        'created_at' => now()->subMinutes(30),
        'updated_at' => now()
    ],
    // In-app logs
    [
        'notificacao_id' => null,
        'nivel' => 'info',
        'mensagem' => 'Notificação in-app exibida ao usuário',
        'dados' => json_encode([
            'user_id' => 12345,
            'notification_type' => 'promotion',
            'display_time' => '3s',
            'ip' => '192.168.1.150',
            'user_agent' => 'WebApp/1.5.2'
        ]),
        'created_at' => now()->subMinutes(8),
        'updated_at' => now()
    ]
];

foreach ($logsParaAdicionar as $log) {
    DB::table('notificacao_logs')->insert($log);
}

echo "✅ Adicionados " . count($logsParaAdicionar) . " logs de demonstração\n";

// Estatísticas finais
$novasEstatisticas = DB::table('notificacao_logs')
    ->select('nivel', DB::raw('COUNT(*) as total'))
    ->groupBy('nivel')
    ->get();

echo "\n📊 Estatísticas atualizadas:\n";
foreach ($novasEstatisticas as $stat) {
    echo "- {$stat->nivel}: {$stat->total}\n";
}

$totalFinal = DB::table('notificacao_logs')->count();
echo "\nTotal de logs: $totalFinal\n";
echo "🎯 Página de logs pronta com dados reais!\n";
