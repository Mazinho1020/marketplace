<?php

require_once 'vendor/autoload.php';

use App\Services\Financial\ContasPagarService;
use App\Models\Financial\LancamentoFinanceiro;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE SERVICE CONTAS A PAGAR ===\n";

try {
    $service = new ContasPagarService();

    // Buscar lancamento 377
    $lancamento = LancamentoFinanceiro::find(377);

    if (!$lancamento) {
        echo "❌ Lançamento 377 não encontrado!\n";
        exit;
    }

    echo "✅ Lançamento encontrado: ID {$lancamento->id}\n";
    echo "💰 Valor final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";

    // Simular dados do pagamento
    $dadosPagamento = [
        'forma_pagamento_id' => 1,
        'bandeira_id' => null,
        'conta_bancaria_id' => 1,
        'valor' => 300.00,
        'valor_principal' => 300.00,
        'valor_juros' => 0,
        'valor_multa' => 0,
        'valor_desconto' => 0,
        'data_pagamento' => date('Y-m-d'),
        'data_compensacao' => null,
        'observacao' => 'Teste via service',
        'comprovante_pagamento' => null,
        'taxa' => 0,
        'valor_taxa' => 0,
        'referencia_externa' => null,
        'usuario_id' => 1
    ];

    echo "\n📤 Dados do pagamento:\n";
    foreach ($dadosPagamento as $key => $value) {
        echo "   $key: $value\n";
    }

    echo "\n=== TESTANDO SERVICE ===\n";

    // Testar sem salvar (dry run)
    $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    $saldoDevedor = $lancamento->valor_final - $valorPago;
    $valorPagamento = $dadosPagamento['valor'];

    echo "💵 Valor já pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n";
    echo "🔴 Saldo devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n";
    echo "💳 Valor pagamento: R$ " . number_format($valorPagamento, 2, ',', '.') . "\n";

    // Verificar condição
    if ($valorPagamento > ($saldoDevedor + 0.01)) {
        echo "❌ VALIDAÇÃO FALHARIA: Valor maior que saldo devedor\n";
    } else {
        echo "✅ VALIDAÇÃO PASSARIA: Pode prosseguir\n";
    }

    // Verificar se método adicionarPagamento existe
    if (method_exists($lancamento, 'adicionarPagamento')) {
        echo "✅ Método adicionarPagamento existe\n";
    } else {
        echo "❌ Método adicionarPagamento NÃO existe\n";
    }

    // Tentar executar o service (sem commit)
    echo "\n=== EXECUTANDO SERVICE (DRY RUN) ===\n";

    try {
        // Não vamos fazer commit real
        \Illuminate\Support\Facades\DB::beginTransaction();

        $pagamento = $service->pagar($lancamento->id, $dadosPagamento);

        echo "✅ Service executou com sucesso!\n";
        echo "📦 Pagamento criado: ID {$pagamento->id}\n";
        echo "💰 Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";

        // Sempre fazer rollback no teste
        \Illuminate\Support\Facades\DB::rollBack();
        echo "🔄 Rollback executado (teste)\n";
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\DB::rollBack();
        echo "❌ Erro no service: " . $e->getMessage() . "\n";
        echo "📍 Arquivo: " . $e->getFile() . " linha " . $e->getLine() . "\n";
        echo "📊 Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . " linha " . $e->getLine() . "\n";
}

echo "\n=== FIM TESTE ===\n";
