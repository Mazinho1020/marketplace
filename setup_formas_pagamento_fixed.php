<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== INSERINDO FORMAS DE PAGAMENTO ===\n";

// Inserir formas de pagamento básicas (usando apenas campos existentes)
$formasPagamento = [
    ['nome' => 'Dinheiro', 'gateway_method' => null, 'empresa_id' => 1],
    ['nome' => 'PIX', 'gateway_method' => 'pix', 'empresa_id' => 1],
    ['nome' => 'Cartão de Crédito', 'gateway_method' => 'credit_card', 'empresa_id' => 1],
    ['nome' => 'Cartão de Débito', 'gateway_method' => 'debit_card', 'empresa_id' => 1],
    ['nome' => 'Boleto', 'gateway_method' => 'bank_slip', 'empresa_id' => 1],
    ['nome' => 'Transferência Bancária', 'gateway_method' => null, 'empresa_id' => 1],
    ['nome' => 'Cheque', 'gateway_method' => null, 'empresa_id' => 1]
];

foreach ($formasPagamento as $forma) {
    try {
        $existente = DB::table('formas_pagamento')
            ->where('nome', $forma['nome'])
            ->where('empresa_id', $forma['empresa_id'])
            ->first();

        if (!$existente) {
            DB::table('formas_pagamento')->insert($forma);
            echo "✅ Forma de pagamento '{$forma['nome']}' inserida\n";
        } else {
            echo "⚠️ Forma de pagamento '{$forma['nome']}' já existe\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro ao inserir '{$forma['nome']}': " . $e->getMessage() . "\n";
    }
}

echo "\n=== FORMAS DE PAGAMENTO CADASTRADAS ===\n";
$formas = DB::select("SELECT * FROM formas_pagamento WHERE empresa_id = 1 ORDER BY nome");
foreach ($formas as $forma) {
    echo "ID: {$forma->id} | {$forma->nome} | Gateway: " . ($forma->gateway_method ?? 'N/A') . "\n";
}

echo "\n✅ Setup concluído!\n";
