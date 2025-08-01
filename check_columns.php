<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $columns = Schema::getColumnListing('programas_fidelidade');
    echo "Colunas da tabela programas_fidelidade:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
