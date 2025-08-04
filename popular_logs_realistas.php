<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== POPULANDO SISTEMA COM LOGS REALISTAS ===\n";

// Buscar algumas notificações existentes
$notificacoes = DB::table('notificacao_enviadas')->get();

// Arrays de dados realistas
$mensagensInfo = [
    'Email enviado com sucesso',
    'SMS entregue ao destinatário',
    'Push notification processada',
    'Template renderizado com sucesso',
    'Webhook executado com sucesso',
    'Notificação in-app exibida',
    'Queue job processado',
    'Cache invalidado com sucesso'
];

$mensagensWarning = [
    'Alta latência detectada no gateway',
    'Rate limit próximo do limite',
    'Template depreciado em uso',
    'Conexão lenta com provedor',
    'Memory usage acima de 80%',
    'Disk space baixo',
    'CPU usage elevado',
    'Queue size crescendo rapidamente'
];

$mensagensError = [
    'Falha ao enviar email - SMTP timeout',
    'Token de push inválido',
    'Gateway SMS retornou erro',
    'Template não encontrado',
    'Database connection failed',
    'API rate limit exceeded',
    'Invalid JSON payload',
    'Authentication failed'
];

$mensagensDebug = [
    'Iniciando processamento de notificação',
    'Validando dados de entrada',
    'Conectando ao provedor externo',
    'Carregando template do cache',
    'Executando validações de negócio',
    'Preparando payload para envio',
    'Registrando métricas de performance',
    'Limpando dados temporários'
];

$mensagensCritical = [
    'Serviço de notificações indisponível',
    'Database connection pool esgotado',
    'Disk space critical (< 5%)',
    'Memory leak detectado',
    'All external providers down'
];

$componentes = ['email', 'sms', 'push', 'in_app', 'database', 'api', 'queue'];

// Gerar logs dos últimos 7 dias
for ($dia = 6; $dia >= 0; $dia--) {
    $dataBase = Carbon::now()->subDays($dia);

    // Gerar logs distribuídos ao longo do dia
    for ($hora = 0; $hora < 24; $hora++) {
        $timestamp = $dataBase->copy()->addHours($hora)->addMinutes(rand(0, 59));

        // Quantidade de logs varia por horário (mais atividade durante o dia)
        $qtdLogs = $hora >= 8 && $hora <= 22 ? rand(3, 8) : rand(1, 3);

        for ($i = 0; $i < $qtdLogs; $i++) {
            $nivel = ['info', 'info', 'info', 'debug', 'debug', 'warning', 'error', 'critical'][rand(0, 7)];

            $mensagem = '';
            switch ($nivel) {
                case 'info':
                    $mensagem = $mensagensInfo[array_rand($mensagensInfo)];
                    break;
                case 'warning':
                    $mensagem = $mensagensWarning[array_rand($mensagensWarning)];
                    break;
                case 'error':
                    $mensagem = $mensagensError[array_rand($mensagensError)];
                    break;
                case 'debug':
                    $mensagem = $mensagensDebug[array_rand($mensagensDebug)];
                    break;
                case 'critical':
                    $mensagem = $mensagensCritical[array_rand($mensagensCritical)];
                    break;
            }

            $componente = $componentes[array_rand($componentes)];
            $notificacaoId = $notificacoes->count() > 0 && rand(1, 3) == 1 ? $notificacoes->random()->id : null;

            $dados = [
                'componente' => $componente,
                'ip' => '192.168.1.' . rand(100, 250),
                'user_agent' => [
                    'NotificationService/2.0',
                    'EmailProvider/1.5',
                    'SMSGateway/3.2',
                    'PushService/1.8',
                    'QueueWorker/2.1',
                    'DatabaseMonitor/1.0',
                    'APIGateway/1.3'
                ][array_rand(['NotificationService/2.0', 'EmailProvider/1.5', 'SMSGateway/3.2', 'PushService/1.8', 'QueueWorker/2.1', 'DatabaseMonitor/1.0', 'APIGateway/1.3'])],
                'response_time' => rand(50, 2000) . 'ms',
                'timestamp' => $timestamp->toISOString()
            ];

            // Adicionar dados específicos por componente
            switch ($componente) {
                case 'email':
                    $dados['smtp_server'] = 'smtp.gmail.com';
                    $dados['message_id'] = 'msg_' . uniqid();
                    break;
                case 'sms':
                    $dados['gateway'] = ['twilio', 'nexmo', 'aws-sns'][rand(0, 2)];
                    $dados['cost'] = '$0.0075';
                    break;
                case 'push':
                    $dados['platform'] = ['android', 'ios'][rand(0, 1)];
                    $dados['token_length'] = rand(140, 180);
                    break;
                case 'database':
                    $dados['query_time'] = rand(10, 1000) . 'ms';
                    $dados['affected_rows'] = rand(1, 100);
                    break;
            }

            DB::table('notificacao_logs')->insert([
                'notificacao_id' => $notificacaoId,
                'nivel' => $nivel,
                'mensagem' => $mensagem,
                'dados' => json_encode($dados),
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }
    }

    echo "✅ Dia $dia concluído\n";
}

// Estatísticas finais
$estatisticas = DB::table('notificacao_logs')
    ->select('nivel', DB::raw('COUNT(*) as total'))
    ->groupBy('nivel')
    ->get();

echo "\n📊 Estatísticas finais:\n";
foreach ($estatisticas as $stat) {
    echo "- {$stat->nivel}: {$stat->total}\n";
}

$total = DB::table('notificacao_logs')->count();
echo "\n🎯 Total de logs criados: $total\n";
echo "✅ Sistema de logs populado com dados realistas!\n";
