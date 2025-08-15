<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Estrutura da tabela formas_pagamento ===\n";
    $columns = DB::select('DESCRIBE formas_pagamento');

    foreach ($columns as $column) {
        echo "Campo: {$column->Field}, Tipo: {$column->Type}, Nulo: {$column->Null}, Padrão: {$column->Default}\n";
    }

    echo "\n=== Dados da tabela formas_pagamento ===\n";
    $formas = DB::table('formas_pagamento')->get();

    foreach ($formas as $forma) {
        echo "ID: {$forma->id}, Nome: {$forma->nome}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
