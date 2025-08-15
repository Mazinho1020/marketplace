<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Models\Financial\Pagamento;

echo "=== TESTE DE RELACIONAMENTOS LANÇAMENTO-PAGAMENTO ===\n";

try {
    // Verificar lançamento 377 que tem pagamentos
    echo "\n1. Testando lançamento 377 (que tem pagamentos):\n";
    $lancamento377 = LancamentoFinanceiro::find(377);

    if ($lancamento377) {
        echo "   ✅ Lançamento 377 encontrado\n";
        echo "   Descrição: {$lancamento377->descricao}\n";
        echo "   Valor: R$ {$lancamento377->valor}\n";
        echo "   Status: {$lancamento377->situacao_financeira->value}\n";

        // Testar relacionamento pagamentos
        echo "\n   Testando relacionamento pagamentos():\n";
        try {
            $pagamentos = $lancamento377->pagamentos;
            echo "   ✅ Relacionamento pagamentos funcionando\n";
            echo "   Total de pagamentos: {$pagamentos->count()}\n";

            foreach ($pagamentos as $pagamento) {
                echo "     -> Pagamento ID: {$pagamento->id}, Valor: R$ {$pagamento->valor}, Status: {$pagamento->status_pagamento}\n";

                // Testar relacionamento forma de pagamento
                try {
                    $formaPag = $pagamento->formaPagamento;
                    if ($formaPag) {
                        echo "        Forma: {$formaPag->nome}\n";
                    } else {
                        echo "        Forma: Não encontrada (ID: {$pagamento->forma_pagamento_id})\n";
                    }
                } catch (Exception $e) {
                    echo "        ❌ Erro ao carregar forma de pagamento: " . $e->getMessage() . "\n";
                }
            }
        } catch (Exception $e) {
            echo "   ❌ Erro no relacionamento pagamentos: " . $e->getMessage() . "\n";
            echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }

        // Testar relacionamento recebimentos
        echo "\n   Testando relacionamento recebimentos():\n";
        try {
            $recebimentos = $lancamento377->recebimentos;
            echo "   ✅ Relacionamento recebimentos funcionando\n";
            echo "   Total de recebimentos: {$recebimentos->count()}\n";
        } catch (Exception $e) {
            echo "   ❌ Erro no relacionamento recebimentos: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ❌ Lançamento 377 não encontrado\n";
    }

    // Testar with() para carregar relacionamentos
    echo "\n2. Testando carregamento com with():\n";
    try {
        $lancamento = LancamentoFinanceiro::with(['pagamentos', 'recebimentos'])->find(377);
        if ($lancamento) {
            echo "   ✅ Carregamento com with() funcionando\n";
            echo "   Pagamentos carregados: {$lancamento->pagamentos->count()}\n";
            echo "   Recebimentos carregados: {$lancamento->recebimentos->count()}\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erro no carregamento com with(): " . $e->getMessage() . "\n";
        echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
