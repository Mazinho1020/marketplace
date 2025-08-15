<?php

require_once 'vendor/autoload.php';

use App\Models\Financial\LancamentoFinanceiro;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG VALIDAÇÃO PAGAMENTO ===\n";

try {
    // Buscar lancamento 377
    $lancamento = LancamentoFinanceiro::find(377);

    if (!$lancamento) {
        echo "❌ Lançamento 377 não encontrado!\n";
        exit;
    }

    echo "✅ Lançamento encontrado: ID {$lancamento->id}\n";
    echo "💰 Valor final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";

    // Reproduzir exata lógica do controller
    $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    $saldoDevedor = $lancamento->valor_final - $valorPago;

    echo "💵 Valor já pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n";
    echo "🔴 Saldo devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n";

    // Testar valores exatos
    $valorTentativa = 300.00; // Valor sendo testado

    echo "\n=== TESTE VALIDAÇÃO ===\n";
    echo "🧮 Valor tentativa: R$ " . number_format($valorTentativa, 2, ',', '.') . "\n";
    echo "🧮 Saldo devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n";

    echo "\n=== COMPARAÇÕES ===\n";
    echo "valor > saldo: $valorTentativa > $saldoDevedor = " . ($valorTentativa > $saldoDevedor ? 'TRUE' : 'FALSE') . "\n";
    echo "valor >= saldo: $valorTentativa >= $saldoDevedor = " . ($valorTentativa >= $saldoDevedor ? 'TRUE' : 'FALSE') . "\n";
    echo "valor == saldo: $valorTentativa == $saldoDevedor = " . ($valorTentativa == $saldoDevedor ? 'TRUE' : 'FALSE') . "\n";

    // Teste de tipos
    echo "\n=== VERIFICAÇÃO DE TIPOS ===\n";
    echo "Tipo valorTentativa: " . gettype($valorTentativa) . " = $valorTentativa\n";
    echo "Tipo saldoDevedor: " . gettype($saldoDevedor) . " = $saldoDevedor\n";

    // Forçar conversão para float
    $valorTentativaFloat = (float)$valorTentativa;
    $saldoDevedorFloat = (float)$saldoDevedor;

    echo "\n=== COMPARAÇÕES COM FLOAT ===\n";
    echo "float valor > float saldo: $valorTentativaFloat > $saldoDevedorFloat = " . ($valorTentativaFloat > $saldoDevedorFloat ? 'TRUE' : 'FALSE') . "\n";
    echo "float valor >= float saldo: $valorTentativaFloat >= $saldoDevedorFloat = " . ($valorTentativaFloat >= $saldoDevedorFloat ? 'TRUE' : 'FALSE') . "\n";
    echo "float valor == float saldo: $valorTentativaFloat == $saldoDevedorFloat = " . ($valorTentativaFloat == $saldoDevedorFloat ? 'TRUE' : 'FALSE') . "\n";

    // Simular exata condição do controller
    echo "\n=== SIMULAÇÃO DO CONTROLLER ===\n";
    if ($valorTentativa > $saldoDevedor) {
        echo "❌ CONTROLLER REJEITARIA: Valor do pagamento (R$ " . number_format($valorTentativa, 2, ',', '.') . ") não pode ser maior que o saldo devedor (R$ " . number_format($saldoDevedor, 2, ',', '.') . ")\n";
    } else {
        echo "✅ CONTROLLER ACEITARIA: Pagamento pode prosseguir\n";
    }

    // Verificar decimal precision
    echo "\n=== VERIFICAÇÃO DECIMAL ===\n";
    echo "Diferença absoluta: " . abs($valorTentativa - $saldoDevedor) . "\n";
    echo "É menor que 0.01? " . (abs($valorTentativa - $saldoDevedor) < 0.01 ? 'SIM' : 'NÃO') . "\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . " linha " . $e->getLine() . "\n";
}
