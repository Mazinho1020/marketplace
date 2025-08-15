<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use Illuminate\Support\Facades\DB;

try {
    echo "=== Testando consultas corrigidas ===\n\n";

    // Teste 1: Formas de pagamento
    echo "1. Testando formas_pagamento:\n";
    $formas = DB::table('formas_pagamento')
        ->where('ativo', true)
        ->select('id', 'nome', 'tipo')
        ->orderBy('nome')
        ->get();

    echo "   - Encontradas: " . $formas->count() . " formas de pagamento\n";
    foreach ($formas->take(3) as $forma) {
        echo "   - ID: {$forma->id}, Nome: {$forma->nome}, Tipo: {$forma->tipo}\n";
    }

    // Teste 2: Contas bancárias
    echo "\n2. Testando conta_bancaria:\n";
    $contas = DB::table('conta_bancaria')
        ->where('empresa_id', 1)
        ->select('id', 'banco', 'agencia', 'numero_conta', 'nome_conta')
        ->orderBy('banco')
        ->get();

    echo "   - Encontradas: " . $contas->count() . " contas bancárias\n";
    foreach ($contas->take(3) as $conta) {
        echo "   - ID: {$conta->id}, Banco: {$conta->banco}, Agência: {$conta->agencia}, Conta: {$conta->numero_conta}, Nome: {$conta->nome_conta}\n";
    }

    // Teste 3: Bandeiras
    echo "\n3. Testando forma_pag_bandeiras:\n";
    $bandeiras = DB::table('forma_pag_bandeiras')
        ->where('ativo', true)
        ->select('id', 'nome')
        ->orderBy('nome')
        ->get();

    echo "   - Encontradas: " . $bandeiras->count() . " bandeiras\n";
    foreach ($bandeiras->take(3) as $bandeira) {
        echo "   - ID: {$bandeira->id}, Nome: {$bandeira->nome}\n";
    }

    echo "\n✅ Todas as consultas funcionaram corretamente!\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
