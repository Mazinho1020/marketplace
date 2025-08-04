<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Verificando estrutura da tabela notificacao_agendamentos...\n";

    $columns = DB::select('DESCRIBE notificacao_agendamentos');

    echo "Colunas encontradas:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }

    echo "\nVerificando se a coluna 'status' existe...\n";
    $hasStatus = false;
    foreach ($columns as $column) {
        if ($column->Field === 'status') {
            $hasStatus = true;
            break;
        }
    }

    if ($hasStatus) {
        echo "✅ Coluna 'status' existe!\n";
    } else {
        echo "❌ Coluna 'status' NÃO existe!\n";
        echo "Colunas relacionadas encontradas:\n";
        foreach ($columns as $column) {
            if (strpos($column->Field, 'status') !== false || strpos($column->Field, 'estado') !== false) {
                echo "- {$column->Field}\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
