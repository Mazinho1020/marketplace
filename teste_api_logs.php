<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTANDO API DE LOGS ===\n";

try {
    // Testar query direta
    $logs = DB::table('notificacao_logs as nl')
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
        ->orderBy('nl.created_at', 'desc')
        ->limit(5)
        ->get();

    echo "Logs encontrados: " . $logs->count() . "\n\n";

    foreach ($logs as $log) {
        $dados = json_decode($log->dados, true) ?? [];
        echo "ID: {$log->id}\n";
        echo "Nível: {$log->nivel}\n";
        echo "Mensagem: {$log->mensagem}\n";
        echo "Componente: " . ($log->componente ?? 'N/A') . "\n";
        echo "IP: " . ($dados['ip'] ?? 'N/A') . "\n";
        echo "Created: {$log->created_at}\n";
        echo "---\n";
    }

    // Testar estatísticas
    echo "\n=== ESTATÍSTICAS ===\n";
    $stats = DB::table('notificacao_logs')
        ->select('nivel', DB::raw('COUNT(*) as total'))
        ->groupBy('nivel')
        ->get();

    foreach ($stats as $stat) {
        echo "{$stat->nivel}: {$stat->total}\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
