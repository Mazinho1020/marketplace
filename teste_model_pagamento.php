<?php

require_once 'vendor/autoload.php';

use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;

// Inicializar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== TESTE MODEL PAGAMENTO - CONTA 377 ===\n\n";

    // Usar o model para buscar o lançamento
    $lancamento = LancamentoFinanceiro::find(377);

    if (!$lancamento) {
        echo "❌ Lançamento 377 não encontrado!\n";
        exit;
    }

    echo "📋 DADOS DO LANÇAMENTO:\n";
    echo "ID: {$lancamento->id}\n";
    echo "Valor Final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
    echo "Situação: {$lancamento->situacao_financeira->value}\n\n";

    // Testar relacionamento pagamentos
    echo "💰 TESTE RELACIONAMENTO PAGAMENTOS:\n";
    $pagamentos = $lancamento->pagamentos;
    echo "Total de pagamentos via relacionamento: " . $pagamentos->count() . "\n";

    // Testar pagamentos confirmados
    $pagamentosConfirmados = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->get();
    echo "Pagamentos confirmados: " . $pagamentosConfirmados->count() . "\n";

    // Calcular valor pago
    $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    echo "Valor total pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n";

    // Calcular saldo devedor
    $saldoDevedor = $lancamento->valor_final - $valorPago;
    echo "Saldo devedor calculado: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n\n";

    // Testar atributo getSaldoDevedorAttribute se existir
    if (method_exists($lancamento, 'getSaldoDevedorAttribute')) {
        echo "Saldo devedor via atributo: R$ " . number_format($lancamento->saldo_devedor, 2, ',', '.') . "\n";
    }

    echo "\n=== TESTE CRIAÇÃO DE PAGAMENTO ===\n";

    // Simular dados de pagamento
    echo "Valor a ser pago: R$ 50,00\n";
    echo "Saldo atual: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n";
    echo "Validação (50 <= {$saldoDevedor}): " . (50 <= $saldoDevedor ? "✅ VÁLIDO" : "❌ INVÁLIDO") . "\n";

    echo "\nValor a ser pago: R$ 100,00\n";
    echo "Validação (100 <= {$saldoDevedor}): " . (100 <= $saldoDevedor ? "✅ VÁLIDO" : "❌ INVÁLIDO") . "\n";

    echo "\nValor a ser pago: R$ 150,00\n";
    echo "Validação (150 <= {$saldoDevedor}): " . (150 <= $saldoDevedor ? "✅ VÁLIDO" : "❌ INVÁLIDO") . "\n";
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
