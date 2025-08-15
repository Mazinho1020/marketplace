<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inicializar o Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== DEBUG CONTA 377 - CORRIGIDO ===\n\n";

    // Buscar dados da conta 377 na tabela correta
    $lancamento = DB::table('lancamentos')
        ->where('id', 377)
        ->first();

    if (!$lancamento) {
        echo "❌ Conta 377 não encontrada na tabela 'lancamentos'!\n";
        exit;
    }

    echo "📋 DADOS DA CONTA:\n";
    echo "ID: {$lancamento->id}\n";
    echo "Valor Original: R$ " . number_format($lancamento->valor_original, 2, ',', '.') . "\n";
    echo "Valor Final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
    echo "Situação: {$lancamento->situacao_financeira}\n";
    echo "Data Vencimento: {$lancamento->data_vencimento}\n";
    echo "Natureza: {$lancamento->natureza_financeira}\n\n";

    // Buscar pagamentos com o campo correto
    $pagamentos = DB::table('pagamentos')
        ->where('lancamento_id', 377)  // Campo correto!
        ->where('status_pagamento', 'confirmado')
        ->get();

    echo "💰 PAGAMENTOS:\n";
    echo "Total de pagamentos: " . count($pagamentos) . "\n";

    $totalPago = 0;
    foreach ($pagamentos as $pagamento) {
        echo "- Pagamento ID {$pagamento->id}: R$ " . number_format($pagamento->valor, 2, ',', '.') . " ({$pagamento->status_pagamento})\n";
        $totalPago += $pagamento->valor;
    }

    echo "Total Pago: R$ " . number_format($totalPago, 2, ',', '.') . "\n";

    // Calcular saldo devedor
    $saldoDevedor = $lancamento->valor_final - $totalPago;
    echo "Saldo Devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n\n";

    // Verificar se há pagamentos com outros status
    $pagamentosOutros = DB::table('pagamentos')
        ->where('lancamento_id', 377)
        ->where('status_pagamento', '!=', 'confirmado')
        ->get();

    if (count($pagamentosOutros) > 0) {
        echo "⚠️ PAGAMENTOS COM OUTROS STATUS:\n";
        foreach ($pagamentosOutros as $pagamento) {
            echo "- Pagamento ID {$pagamento->id}: R$ " . number_format($pagamento->valor, 2, ',', '.') . " (Status: {$pagamento->status_pagamento})\n";
        }
        echo "\n";
    }

    echo "✅ RESULTADO ESPERADO:\n";
    echo "- Como não há pagamentos, o saldo devedor deveria ser: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";
    echo "- Status deveria permitir pagamentos até esse valor.\n";
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
