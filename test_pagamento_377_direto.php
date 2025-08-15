<?php

require_once 'vendor/autoload.php';

use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DIRETO PAGAMENTO CONTA 377 ===\n";

try {
    // Buscar lancamento
    $lancamento = LancamentoFinanceiro::find(377);

    if (!$lancamento) {
        echo "❌ Lançamento 377 não encontrado!\n";
        exit;
    }

    echo "✅ Lançamento encontrado: ID {$lancamento->id}\n";
    echo "📄 Descrição: {$lancamento->descricao}\n";
    echo "💰 Valor final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";

    // Calcular saldo devedor atual
    $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    $saldoDevedor = $lancamento->valor_final - $valorPago;

    echo "💵 Valor já pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n";
    echo "🔴 Saldo devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n";

    // Simular dados do pagamento
    $valorPagamento = 300.00; // Valor que está sendo testado

    echo "\n=== SIMULANDO PAGAMENTO ===\n";
    echo "💳 Valor do pagamento: R$ " . number_format($valorPagamento, 2, ',', '.') . "\n";

    // Teste da condição
    if ($valorPagamento > $saldoDevedor) {
        echo "❌ FALHA: Valor do pagamento (R$ " . number_format($valorPagamento, 2, ',', '.') . ") é maior que saldo devedor (R$ " . number_format($saldoDevedor, 2, ',', '.') . ")\n";
    } else {
        echo "✅ SUCESSO: Pagamento pode ser processado!\n";

        // Simular criação do pagamento
        echo "\n=== CRIANDO PAGAMENTO ===\n";

        DB::beginTransaction();

        try {
            $numeroParcela = $lancamento->pagamentos()->max('numero_parcela_pagamento') + 1;

            $pagamento = new Pagamento([
                'empresa_id' => 1,
                'lancamento_id' => $lancamento->id,
                'forma_pagamento_id' => 1, // PIX por exemplo
                'bandeira_id' => null,
                'numero_parcela_pagamento' => $numeroParcela,
                'valor' => $valorPagamento,
                'valor_principal' => $valorPagamento,
                'valor_juros' => 0,
                'valor_multa' => 0,
                'valor_desconto' => 0,
                'conta_bancaria_id' => 1,
                'taxa' => 0,
                'valor_taxa' => 0,
                'data_pagamento' => now(),
                'data_compensacao' => null,
                'status_pagamento' => 'confirmado',
                'observacao' => 'Teste de pagamento',
                'referencia_externa' => null
            ]);

            // Apenas salvar se não for teste
            if (false) { // Manter como false para não salvar de verdade
                $pagamento->save();
                echo "💾 Pagamento salvo com ID: {$pagamento->id}\n";
            } else {
                echo "💾 Pagamento simulado (não salvo)\n";
            }

            // Recalcular após pagamento
            $novoValorPago = $valorPago + $valorPagamento;
            $novoSaldoDevedor = $lancamento->valor_final - $novoValorPago;

            echo "📊 Novo valor pago: R$ " . number_format($novoValorPago, 2, ',', '.') . "\n";
            echo "📊 Novo saldo devedor: R$ " . number_format($novoSaldoDevedor, 2, ',', '.') . "\n";

            // Atualizar situação do lançamento
            if ($novoSaldoDevedor <= 0) {
                echo "🎉 Conta seria quitada totalmente!\n";
            } elseif ($novoValorPago > 0) {
                echo "⚠️ Conta ficaria parcialmente paga\n";
            }

            DB::rollBack(); // Sempre fazer rollback no teste
            echo "✅ Teste concluído com sucesso!\n";
        } catch (Exception $e) {
            DB::rollBack();
            echo "❌ Erro ao criar pagamento: " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== VERIFICAÇÃO FINAL ===\n";
    echo "🔍 Comparação: $valorPagamento > $saldoDevedor = " . ($valorPagamento > $saldoDevedor ? 'true' : 'false') . "\n";
    echo "🔍 Comparação: $valorPagamento >= $saldoDevedor = " . ($valorPagamento >= $saldoDevedor ? 'true' : 'false') . "\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . " linha " . $e->getLine() . "\n";
}
