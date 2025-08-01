<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "📋 ESTRUTURA DAS TABELAS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE config_db_connections');

    echo "Colunas da tabela config_db_connections:\n";
    foreach ($columns as $col) {
        echo "  • {$col->Field} ({$col->Type})\n";
    }

    echo "\n📋 PRIMEIROS REGISTROS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $connections = \Illuminate\Support\Facades\DB::table('config_db_connections')
        ->select('*')
        ->limit(5)
        ->get();

    foreach ($connections as $conn) {
        echo "ID {$conn->id}:\n";
        foreach ($conn as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
