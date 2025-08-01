<?php
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Schema;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ESTRUTURA DA TABELA empresas ===\n\n";

if (Schema::hasTable('empresas')) {
    $columns = Schema::getColumnListing('empresas');
    echo "Colunas encontradas:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
} else {
    echo "Tabela empresas não existe\n";
}

echo "\n=== ESTRUTURA DA TABELA fidelidade_cashback_regras ===\n\n";

$columns2 = Schema::getColumnListing('fidelidade_cashback_regras');

echo "Colunas encontradas:\n";
foreach ($columns2 as $column) {
    echo "- $column\n";
}
