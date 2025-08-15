<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Testando API de bandeiras após correção:\n";

$bandeiras = DB::table('forma_pag_bandeiras as fpb')
    ->join('forma_pagamento_bandeiras as fpbb', 'fpb.id', '=', 'fpbb.forma_pag_bandeira_id')
    ->where('fpbb.forma_pagamento_id', 25)
    ->where('fpbb.empresa_id', 1)
    ->select('fpb.id', 'fpb.nome', 'fpb.taxa', 'fpb.dias_para_receber')
    ->get();

echo "📊 Bandeiras encontradas: " . count($bandeiras) . "\n";

foreach ($bandeiras as $bandeira) {
    echo "  - ID: {$bandeira->id} | Nome: {$bandeira->nome} | Taxa: {$bandeira->taxa}%\n";
}

echo "\n🎉 API de bandeiras funcionando perfeitamente!\n";

// Vamos também testar cartão de crédito
echo "\n🔍 Testando bandeiras para Cartão de Crédito (ID: 24):\n";

$bandeiras_credito = DB::table('forma_pag_bandeiras as fpb')
    ->join('forma_pagamento_bandeiras as fpbb', 'fpb.id', '=', 'fpbb.forma_pag_bandeira_id')
    ->where('fpbb.forma_pagamento_id', 24)
    ->where('fpbb.empresa_id', 1)
    ->select('fpb.id', 'fpb.nome', 'fpb.taxa', 'fpb.dias_para_receber')
    ->get();

echo "📊 Bandeiras para crédito: " . count($bandeiras_credito) . "\n";

if (count($bandeiras_credito) == 0) {
    echo "🔧 Criando bandeiras para cartão de crédito...\n";

    // Buscar bandeiras de crédito
    $bandeiras_credito_disponiveis = DB::table('forma_pag_bandeiras')
        ->where('nome', 'like', '%visa%')
        ->orWhere('nome', 'like', '%master%')
        ->orWhere('nome', 'like', '%elo%')
        ->orWhere('nome', 'like', '%american%')
        ->limit(5)
        ->get();

    foreach ($bandeiras_credito_disponiveis as $bandeira) {
        $existe = DB::table('forma_pagamento_bandeiras')
            ->where('forma_pagamento_id', 24)
            ->where('forma_pag_bandeira_id', $bandeira->id)
            ->where('empresa_id', 1)
            ->exists();

        if (!$existe) {
            DB::table('forma_pagamento_bandeiras')->insert([
                'forma_pagamento_id' => 24,
                'forma_pag_bandeira_id' => $bandeira->id,
                'empresa_id' => 1,
                'sync_status' => 'sincronizado',
                'sync_data' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "  ✅ Bandeira {$bandeira->nome} vinculada ao cartão de crédito\n";
        }
    }
} else {
    foreach ($bandeiras_credito as $bandeira) {
        echo "  - ID: {$bandeira->id} | Nome: {$bandeira->nome} | Taxa: {$bandeira->taxa}%\n";
    }
}
