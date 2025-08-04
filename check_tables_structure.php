<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== VERIFICANDO ESTRUTURAS DAS TABELAS ===\n\n";

    // Verificar tabela empresas
    echo "1. TABELA EMPRESAS:\n";
    $empresas = DB::select('DESCRIBE empresas');
    foreach ($empresas as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }

    echo "\n2. TABELA AFI_PLAN_TRANSACOES:\n";
    $transacoes = DB::select('DESCRIBE afi_plan_transacoes');
    foreach ($transacoes as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }

    echo "\n3. TABELA AFI_PLAN_GATEWAYS:\n";
    $gateways = DB::select('DESCRIBE afi_plan_gateways');
    foreach ($gateways as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
