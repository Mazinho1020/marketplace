<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "=== TABELAS EXISTENTES NO BANCO ===\n";
    $tables = Schema::getTableListing();

    echo "Total de tabelas: " . count($tables) . "\n\n";

    foreach ($tables as $table) {
        echo "- $table\n";
    }

    echo "\n=== VERIFICANDO TABELAS ESPECÍFICAS ===\n";

    $tablesToCheck = [
        'pagamentos',
        'recebimentos',
        'lancamentos',
        'lancamento_financeiro',
        'migrations'
    ];

    foreach ($tablesToCheck as $table) {
        $exists = Schema::hasTable($table);
        echo "- $table: " . ($exists ? 'EXISTE' : 'NÃO EXISTE') . "\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
