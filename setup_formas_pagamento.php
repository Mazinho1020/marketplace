<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CRIANDO FORMAS DE PAGAMENTO BÁSICAS ===\n";

// Criar tabela simples se não existir
try {
    DB::statement("
        CREATE TABLE IF NOT EXISTS formas_pagamento (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            ativo BOOLEAN DEFAULT 1,
            icone VARCHAR(50) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Tabela formas_pagamento criada/verificada\n";
} catch (Exception $e) {
    echo "❌ Erro ao criar tabela: " . $e->getMessage() . "\n";
}

// Inserir formas de pagamento básicas
$formasPagamento = [
    ['nome' => 'Dinheiro', 'codigo' => 'cash', 'icone' => 'fas fa-money-bill'],
    ['nome' => 'PIX', 'codigo' => 'pix', 'icone' => 'fas fa-qrcode'],
    ['nome' => 'Cartão de Crédito', 'codigo' => 'credit_card', 'icone' => 'fas fa-credit-card'],
    ['nome' => 'Cartão de Débito', 'codigo' => 'debit_card', 'icone' => 'fas fa-credit-card'],
    ['nome' => 'Boleto', 'codigo' => 'bank_slip', 'icone' => 'fas fa-barcode'],
    ['nome' => 'Transferência', 'codigo' => 'bank_transfer', 'icone' => 'fas fa-university'],
    ['nome' => 'Cheque', 'codigo' => 'check', 'icone' => 'fas fa-money-check']
];

foreach ($formasPagamento as $forma) {
    try {
        DB::table('formas_pagamento')->updateOrInsert(
            ['codigo' => $forma['codigo']],
            $forma
        );
        echo "✅ Forma de pagamento '{$forma['nome']}' inserida/atualizada\n";
    } catch (Exception $e) {
        echo "❌ Erro ao inserir '{$forma['nome']}': " . $e->getMessage() . "\n";
    }
}

echo "\n=== FORMAS DE PAGAMENTO CADASTRADAS ===\n";
$formas = DB::select("SELECT * FROM formas_pagamento ORDER BY nome");
foreach ($formas as $forma) {
    echo "ID: {$forma->id} | {$forma->nome} ({$forma->codigo})\n";
}

echo "\n✅ Setup concluído!\n";
