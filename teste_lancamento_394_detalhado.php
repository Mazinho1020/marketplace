<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;

echo "=== TESTE ESPECÍFICO LANÇAMENTO 394 ===\n";

try {
    echo "\n1. Carregando lançamento 394 simples:\n";
    $lancamento394 = LancamentoFinanceiro::find(394);

    if ($lancamento394) {
        echo "   ✅ Lançamento 394 encontrado\n";
        echo "   Status: {$lancamento394->situacao_financeira->value}\n";

        echo "\n2. Testando relacionamentos individualmente:\n";

        // Pagamentos
        try {
            $pagamentos = $lancamento394->pagamentos;
            echo "   ✅ Pagamentos: {$pagamentos->count()}\n";
        } catch (Exception $e) {
            echo "   ❌ Erro pagamentos: " . $e->getMessage() . "\n";
        }

        // Recebimentos  
        try {
            $recebimentos = $lancamento394->recebimentos;
            echo "   ✅ Recebimentos: {$recebimentos->count()}\n";
        } catch (Exception $e) {
            echo "   ❌ Erro recebimentos: " . $e->getMessage() . "\n";
        }

        echo "\n3. Testando carregamento com with():\n";
        try {
            $lancamento = LancamentoFinanceiro::with(['pagamentos', 'recebimentos', 'empresa', 'contaGerencial', 'pessoa'])
                ->find(394);
            echo "   ✅ Carregamento completo funcionando\n";

            // Simular o que o controller faz
            echo "\n4. Simulando controller show():\n";
            $recebimentos = $lancamento->recebimentos()
                ->where('status_recebimento', 'confirmado')
                ->with(['formaPagamento', 'bandeira', 'contaBancaria'])
                ->orderBy('data_recebimento', 'desc')
                ->get();
            echo "   ✅ Query do controller funcionando\n";
            echo "   Recebimentos confirmados: {$recebimentos->count()}\n";
        } catch (Exception $e) {
            echo "   ❌ Erro: " . $e->getMessage() . "\n";
            echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    } else {
        echo "   ❌ Lançamento 394 não encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
