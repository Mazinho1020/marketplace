<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TABELAS RELACIONADAS A PAGAMENTO ===\n";

$tables = collect(DB::select('SHOW TABLES'))->pluck('Tables_in_marketplace');

$paymentTables = $tables->filter(function ($table) {
    return str_contains($table, 'pagamento') ||
        str_contains($table, 'forma') ||
        str_contains($table, 'payment');
});

echo "Tabelas encontradas:\n";
foreach ($paymentTables as $table) {
    echo "- $table\n";
}

echo "\n=== VERIFICANDO ENUM DE PAYMENT METHOD ===\n";

if (enum_exists('App\Enums\Payment\PaymentMethod')) {
    echo "Enum PaymentMethod existe!\n";
    foreach (App\Enums\Payment\PaymentMethod::cases() as $method) {
        echo "- {$method->value}: {$method->label()}\n";
    }
} else {
    echo "Enum PaymentMethod não encontrado.\n";
}

echo "\n=== ESTRUTURA DA TABELA PAGAMENTOS ===\n";
try {
    $columns = DB::select("DESCRIBE pagamentos");
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
} catch (Exception $e) {
    echo "Tabela 'pagamentos' não encontrada: " . $e->getMessage() . "\n";
}

echo "\n=== CAMPO FORMA_PAGAMENTO_ID ===\n";
try {
    $result = DB::select("SELECT COUNT(*) as count FROM pagamentos WHERE forma_pagamento_id IS NOT NULL");
    echo "Registros com forma_pagamento_id: " . $result[0]->count . "\n";
} catch (Exception $e) {
    echo "Erro ao verificar forma_pagamento_id: " . $e->getMessage() . "\n";
}
