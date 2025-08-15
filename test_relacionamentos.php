<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\Recebimento;

try {
    echo "=== TESTE DE RELACIONAMENTOS ===\n";

    // Primeiro, vamos verificar se existe algum recebimento
    $totalRecebimentos = Recebimento::count();
    echo "Total de recebimentos: $totalRecebimentos\n";

    if ($totalRecebimentos > 0) {
        // Vamos tentar carregar sem relacionamentos primeiro
        echo "\n1. Carregando sem relacionamentos:\n";
        $recebimento = Recebimento::first();
        echo "   ID: " . $recebimento->id . "\n";
        echo "   Forma Pagamento ID: " . $recebimento->forma_pagamento_id . "\n";
        echo "   Bandeira ID: " . $recebimento->bandeira_id . "\n";

        // Agora vamos tentar carregar com relacionamentos
        echo "\n2. Testando relacionamento formaPagamento:\n";
        try {
            $recebimentoComForma = Recebimento::with('formaPagamento')->first();
            echo "   Carregou com sucesso!\n";
            if ($recebimentoComForma->formaPagamento) {
                echo "   Nome da forma: " . $recebimentoComForma->formaPagamento->nome . "\n";
            } else {
                echo "   Forma de pagamento é NULL\n";
            }
        } catch (Exception $e) {
            echo "   ERRO ao carregar formaPagamento: " . $e->getMessage() . "\n";
            echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }

        echo "\n3. Testando relacionamento bandeira:\n";
        try {
            $recebimentoComBandeira = Recebimento::with('bandeira')->first();
            echo "   Carregou com sucesso!\n";
            if ($recebimentoComBandeira->bandeira) {
                echo "   Nome da bandeira: " . $recebimentoComBandeira->bandeira->nome . "\n";
            } else {
                echo "   Bandeira é NULL\n";
            }
        } catch (Exception $e) {
            echo "   ERRO ao carregar bandeira: " . $e->getMessage() . "\n";
            echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }

        echo "\n4. Testando ambos relacionamentos:\n";
        try {
            $recebimentoCompleto = Recebimento::with(['formaPagamento', 'bandeira'])->first();
            echo "   Carregou com sucesso!\n";
        } catch (Exception $e) {
            echo "   ERRO ao carregar ambos: " . $e->getMessage() . "\n";
            echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }
} catch (Exception $e) {
    echo "ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
