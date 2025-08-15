<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Verificando bandeiras para Cartão de Débito (ID: 25)...\n";

// Verificar se existe relacionamento
$relacoes = DB::table('forma_pagamento_bandeiras')
    ->where('forma_pagamento_id', 25)
    ->get();

echo "📊 Relacionamentos encontrados: " . count($relacoes) . "\n";

if (count($relacoes) > 0) {
    foreach ($relacoes as $relacao) {
        $bandeira = DB::table('forma_pag_bandeiras')
            ->where('id', $relacao->bandeira_id)
            ->first();
        echo "  - Bandeira ID: {$relacao->bandeira_id} - {$bandeira->nome}\n";
    }
} else {
    echo "⚠️ Nenhum relacionamento encontrado! Vamos criar...\n";

    // Buscar bandeiras adequadas para cartão de débito
    $bandeiras_cartao = DB::table('forma_pag_bandeiras')
        ->whereIn('nome', ['Visa', 'Mastercard', 'Elo', 'American Express', 'Hipercard'])
        ->get();

    echo "💳 Bandeiras de cartão encontradas: " . count($bandeiras_cartao) . "\n";

    foreach ($bandeiras_cartao as $bandeira) {
        DB::table('forma_pagamento_bandeiras')->insert([
            'forma_pagamento_id' => 25,
            'bandeira_id' => $bandeira->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Vinculado: {$bandeira->nome} (ID: {$bandeira->id})\n";
    }
}

// Vamos também verificar e corrigir o cartão de crédito
echo "\n🔍 Verificando bandeiras para Cartão de Crédito (ID: 24)...\n";

$relacoes_credito = DB::table('forma_pagamento_bandeiras')
    ->where('forma_pagamento_id', 24)
    ->get();

echo "📊 Relacionamentos encontrados: " . count($relacoes_credito) . "\n";

if (count($relacoes_credito) == 0) {
    echo "⚠️ Nenhum relacionamento encontrado para crédito! Vamos criar...\n";

    foreach ($bandeiras_cartao as $bandeira) {
        DB::table('forma_pagamento_bandeiras')->insert([
            'forma_pagamento_id' => 24,
            'bandeira_id' => $bandeira->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "  ✅ Vinculado: {$bandeira->nome} (ID: {$bandeira->id})\n";
    }
}

echo "\n🎉 Configuração de bandeiras concluída!\n";
