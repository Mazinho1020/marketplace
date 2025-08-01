<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Inicializar Laravel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "=== TABELAS DE CONFIGURAÇÃO ===\n\n";

    // Ambientes
    echo "🏠 AMBIENTES:\n";
    $envs = \Illuminate\Support\Facades\DB::table('config_environments')->get();
    foreach ($envs as $env) {
        $prod = $env->is_producao ? 'Prod' : 'Dev';
        $ativo = $env->ativo ? '✅' : '❌';
        echo "• ID: {$env->id} | {$env->codigo} | {$env->nome} | {$prod} | {$ativo}\n";
    }

    echo "\n🔌 CONEXÕES:\n";
    $conns = \Illuminate\Support\Facades\DB::table('config_db_connections')
        ->whereNull('deleted_at')
        ->get();
    foreach ($conns as $conn) {
        $padrao = $conn->padrao ? '⭐' : '  ';
        echo "• ID: {$conn->id} | Env: {$conn->ambiente_id} | {$conn->nome} | {$conn->host}:{$conn->porta} | {$conn->banco} | {$padrao}\n";
    }

    echo "\n💡 Para testar dinamicamente:\n";
    echo "1. Execute: php teste_dinamico.php\n";
    echo "2. Modifique registros nas tabelas acima\n";
    echo "3. Pressione ENTER no teste dinâmico\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
