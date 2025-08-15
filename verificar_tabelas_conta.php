<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TABELAS RELACIONADAS A CONTA ===\n";

try {
    $tables = DB::select('SHOW TABLES');

    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        if (str_contains(strtolower($tableName), 'conta')) {
            echo "✅ " . $tableName . "\n";
        }
    }

    echo "\n=== VERIFICANDO TABELAS ESPECÍFICAS ===\n";

    // Verificar algumas possibilidades
    $possibleTables = [
        'contas_bancarias',
        'conta_bancaria',
        'contas_gerenciais',
        'conta_gerencial',
        'contas_bancos',
        'bancos_contas'
    ];

    foreach ($possibleTables as $tableName) {
        try {
            $result = DB::select("SHOW TABLES LIKE '$tableName'");
            if (!empty($result)) {
                echo "✅ EXISTE: $tableName\n";

                // Mostrar estrutura da tabela
                $columns = DB::select("DESCRIBE $tableName");
                echo "   Colunas:\n";
                foreach ($columns as $col) {
                    echo "     - {$col->Field} ({$col->Type})\n";
                }
            } else {
                echo "❌ NÃO EXISTE: $tableName\n";
            }
        } catch (Exception $e) {
            echo "❌ ERRO ao verificar $tableName: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
