<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICAÇÃO DE TABELAS ===\n\n";

try {
    // Listar todas as tabelas
    $tables = DB::select('SHOW TABLES');

    echo "TABELAS ENCONTRADAS:\n";
    foreach ($tables as $table) {
        $tableArray = (array)$table;
        $tableName = array_values($tableArray)[0];
        echo "- $tableName\n";
    }

    echo "\n=== VERIFICAÇÃO ESPECÍFICA ===\n";

    // Verificar especificamente se existem as tabelas importantes
    $importantTables = [
        'empresa_usuarios',
        'empresa_usuario_tipos',
        'empresa_usuario_tipo_rel',
        'fidelidade_carteiras',
        'fidelidade_cashback_regras',
        'migrations'
    ];

    foreach ($importantTables as $tableName) {
        try {
            $exists = DB::select("SELECT 1 FROM $tableName LIMIT 1");
            echo "✅ $tableName - EXISTE\n";
        } catch (Exception $e) {
            echo "❌ $tableName - NÃO EXISTE\n";
        }
    }

    echo "\n=== STATUS DAS MIGRATIONS ===\n";
    try {
        $migrations = DB::select('SELECT migration FROM migrations ORDER BY batch DESC LIMIT 10');
        echo "ÚLTIMAS MIGRATIONS EXECUTADAS:\n";
        foreach ($migrations as $migration) {
            echo "- $migration->migration\n";
        }
    } catch (Exception $e) {
        echo "❌ Tabela migrations não existe ou não tem dados\n";
    }
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
