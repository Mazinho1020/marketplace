<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICANDO CONTAS A PAGAR ===\n";

try {
    $contas = \App\Models\Financial\LancamentoFinanceiro::where('natureza_financeira', \App\Enums\NaturezaFinanceiraEnum::PAGAR)
        ->select('id', 'empresa_id', 'descricao', 'natureza_financeira')
        ->take(10)
        ->get();
    
    echo "Total de contas a pagar: " . $contas->count() . "\n";
    
    foreach ($contas as $conta) {
        echo "ID: {$conta->id}, Empresa: {$conta->empresa_id}, Descrição: {$conta->descricao}\n";
    }
    
    echo "\n=== VERIFICANDO EMPRESA ID 1 ===\n";
    
    $contasEmpresa1 = \App\Models\Financial\LancamentoFinanceiro::where('empresa_id', 1)
        ->where('natureza_financeira', \App\Enums\NaturezaFinanceiraEnum::PAGAR)
        ->select('id', 'descricao')
        ->take(5)
        ->get();
    
    echo "Contas a pagar da empresa 1: " . $contasEmpresa1->count() . "\n";
    
    foreach ($contasEmpresa1 as $conta) {
        echo "ID: {$conta->id}, Descrição: {$conta->descricao}\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
