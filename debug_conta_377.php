<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inicializar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== TABELAS DO BANCO ===\n\n";

    $tables = DB::select('SHOW TABLES');

    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "📋 $tableName\n";
    }

    echo "\n=== ESTRUTURA DA TABELA pagamentos ===\n\n";

    $columns = DB::select("DESCRIBE pagamentos");
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }

    echo "\n=== BUSCAR TABELA DE LANÇAMENTOS ===\n\n";

    // Procurar tabelas com "lancamento" no nome
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos($tableName, 'lancamento') !== false) {
            echo "🎯 Encontrada: $tableName\n";

            // Mostrar estrutura
            $columns = DB::select("DESCRIBE $tableName");
            foreach ($columns as $column) {
                echo "  - {$column->Field} ({$column->Type})\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
