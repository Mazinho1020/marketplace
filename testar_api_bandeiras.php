<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Testando API de bandeiras para forma 25 (Cartão de Débito):\n";

// Simular a query da API
$bandeiras = DB::table('forma_pag_bandeiras as fpb')
    ->join('forma_pagamento_bandeiras as fpbb', 'fpb.id', '=', 'fpbb.forma_pag_bandeira_id')
    ->where('fpbb.forma_pagamento_id', 25)
    ->where('fpbb.empresa_id', 1)
    ->select('fpb.id', 'fpb.nome', 'fpb.icone', 'fpb.descricao')
    ->get();

echo "📊 Bandeiras encontradas: " . count($bandeiras) . "\n";

foreach ($bandeiras as $bandeira) {
    echo "  - ID: {$bandeira->id} | Nome: {$bandeira->nome} | Ícone: {$bandeira->icone}\n";
}

if (count($bandeiras) == 0) {
    echo "\n⚠️ Problema identificado! Vamos verificar os dados...\n";

    // Verificar relacionamentos
    $relacoes = DB::table('forma_pagamento_bandeiras')
        ->where('forma_pagamento_id', 25)
        ->where('empresa_id', 1)
        ->get();

    echo "📋 Relacionamentos na empresa 1: " . count($relacoes) . "\n";

    if (count($relacoes) == 0) {
        echo "❌ Não há relacionamentos para empresa_id = 1\n";
        echo "🔧 Criando relacionamentos para empresa 1...\n";

        $bandeiras_debito = [33, 34, 35]; // IDs das bandeiras de débito

        foreach ($bandeiras_debito as $bandeira_id) {
            DB::table('forma_pagamento_bandeiras')->insert([
                'forma_pagamento_id' => 25,
                'forma_pag_bandeira_id' => $bandeira_id,
                'empresa_id' => 1,
                'sync_status' => 'sincronizado',
                'sync_data' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "  ✅ Relacionamento criado: forma 25 -> bandeira {$bandeira_id} -> empresa 1\n";
        }

        // Testar novamente
        echo "\n🔄 Testando API novamente...\n";
        $bandeiras = DB::table('forma_pag_bandeiras as fpb')
            ->join('forma_pagamento_bandeiras as fpbb', 'fpb.id', '=', 'fpbb.forma_pag_bandeira_id')
            ->where('fpbb.forma_pagamento_id', 25)
            ->where('fpbb.empresa_id', 1)
            ->select('fpb.id', 'fpb.nome', 'fpb.icone', 'fpb.descricao')
            ->get();

        echo "📊 Bandeiras encontradas após correção: " . count($bandeiras) . "\n";
        foreach ($bandeiras as $bandeira) {
            echo "  - ID: {$bandeira->id} | Nome: {$bandeira->nome} | Ícone: {$bandeira->icone}\n";
        }
    } else {
        echo "📋 Relacionamentos existentes:\n";
        foreach ($relacoes as $relacao) {
            echo "  - Forma: {$relacao->forma_pagamento_id} | Bandeira: {$relacao->forma_pag_bandeira_id} | Empresa: {$relacao->empresa_id}\n";
        }
    }
}
