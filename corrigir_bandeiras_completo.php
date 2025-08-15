<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Estrutura da tabela forma_pag_bandeiras:\n";
$columns = DB::select('DESCRIBE forma_pag_bandeiras');
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

echo "\n📊 Alguns registros de bandeiras:\n";
$bandeiras = DB::table('forma_pag_bandeiras')->limit(5)->get();
foreach ($bandeiras as $bandeira) {
    echo "  " . json_encode($bandeira) . "\n";
}

echo "\n🔍 Testando API simplificada para forma 25:\n";

// Query corrigida sem a coluna 'icone'
$bandeiras = DB::table('forma_pag_bandeiras as fpb')
    ->join('forma_pagamento_bandeiras as fpbb', 'fpb.id', '=', 'fpbb.forma_pag_bandeira_id')
    ->where('fpbb.forma_pagamento_id', 25)
    ->where('fpbb.empresa_id', 1)
    ->select('fpb.id', 'fpb.nome')
    ->get();

echo "📊 Bandeiras encontradas: " . count($bandeiras) . "\n";

foreach ($bandeiras as $bandeira) {
    echo "  - ID: {$bandeira->id} | Nome: {$bandeira->nome}\n";
}

if (count($bandeiras) == 0) {
    echo "\n⚠️ Nenhuma bandeira encontrada para empresa 1!\n";
    echo "🔧 Criando relacionamentos para empresa 1...\n";

    // Verificar se as bandeiras existem
    $bandeiras_existentes = DB::table('forma_pag_bandeiras')
        ->whereIn('id', [33, 34, 35])
        ->get();

    echo "Bandeiras existentes:\n";
    foreach ($bandeiras_existentes as $bandeira) {
        echo "  - ID: {$bandeira->id} | Nome: {$bandeira->nome}\n";

        // Criar relacionamento se não existir
        $existe = DB::table('forma_pagamento_bandeiras')
            ->where('forma_pagamento_id', 25)
            ->where('forma_pag_bandeira_id', $bandeira->id)
            ->where('empresa_id', 1)
            ->exists();

        if (!$existe) {
            DB::table('forma_pagamento_bandeiras')->insert([
                'forma_pagamento_id' => 25,
                'forma_pag_bandeira_id' => $bandeira->id,
                'empresa_id' => 1,
                'sync_status' => 'sincronizado',
                'sync_data' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "    ✅ Relacionamento criado!\n";
        } else {
            echo "    ℹ️ Relacionamento já existe\n";
        }
    }
}
