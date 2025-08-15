<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ESTRUTURA DA TABELA RECEBIMENTOS ===\n";
    $columns = DB::getSchemaBuilder()->getColumnListing('recebimentos');
    foreach ($columns as $column) {
        echo "- $column\n";
    }

    echo "\n=== AMOSTRA DE DADOS DA TABELA RECEBIMENTOS ===\n";
    $sample = DB::table('recebimentos')->first();
    if ($sample) {
        foreach ((array)$sample as $field => $value) {
            echo "$field: $value\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
