<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ESTRUTURA DA TABELA LANCAMENTOS ===\n";
    $columns = DB::getSchemaBuilder()->getColumnListing('lancamentos');
    foreach ($columns as $column) {
        echo "- $column\n";
    }

    echo "\n=== AMOSTRA DE DADOS DA TABELA LANCAMENTOS ===\n";
    $sample = DB::table('lancamentos')->first();
    if ($sample) {
        foreach ((array)$sample as $field => $value) {
            echo "$field: $value\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
