<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inicializar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Inserindo formas de pagamento para RECEBIMENTOS...\n\n";

// Formas de pagamento para recebimentos
$formasRecebimento = [
    ['nome' => 'Dinheiro', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'PIX', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'Transferência Bancária', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'Depósito Bancário', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'Cartão de Crédito', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'Cartão de Débito', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'Boleto Bancário', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'Cheque', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'TED', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
    ['nome' => 'DOC', 'empresa_id' => 1, 'tipo' => 'recebimento', 'origem' => 'sistema'],
];

try {
    DB::beginTransaction();

    foreach ($formasRecebimento as $index => $forma) {
        // Verificar se já existe
        $existe = DB::table('formas_pagamento')
            ->where('nome', $forma['nome'])
            ->where('empresa_id', $forma['empresa_id'])
            ->where('tipo', 'recebimento')
            ->exists();

        if (!$existe) {
            $id = DB::table('formas_pagamento')->insertGetId([
                'nome' => $forma['nome'],
                'empresa_id' => $forma['empresa_id'],
                'tipo' => $forma['tipo'],
                'origem' => $forma['origem'],
                'ativo' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "✅ " . ($index + 1) . ". {$forma['nome']} (ID: $id)\n";
        } else {
            echo "⚠️ " . ($index + 1) . ". {$forma['nome']} (já existe)\n";
        }
    }

    DB::commit();

    echo "\n🎉 Formas de pagamento para recebimentos inseridas com sucesso!\n\n";

    // Verificar total inserido
    $totalRecebimento = DB::table('formas_pagamento')
        ->where('empresa_id', 1)
        ->where('tipo', 'recebimento')
        ->where('ativo', 1)
        ->count();

    echo "📊 Total de formas de recebimento ativas: $totalRecebimento\n";

    // Listar todas as formas de recebimento
    echo "\n📋 Formas de recebimento disponíveis:\n";
    $formas = DB::table('formas_pagamento')
        ->where('empresa_id', 1)
        ->where('tipo', 'recebimento')
        ->where('ativo', 1)
        ->orderBy('nome')
        ->get(['id', 'nome']);

    foreach ($formas as $forma) {
        echo "   ID {$forma->id}: {$forma->nome}\n";
    }
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n✅ Script finalizado!\n";
