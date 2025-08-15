<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 Estrutura da tabela forma_pagamento_bandeiras:\n";
$columns = DB::select('DESCRIBE forma_pagamento_bandeiras');
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})\n";
}

echo "\n📊 Primeiros registros:\n";
$registros = DB::table('forma_pagamento_bandeiras')->limit(5)->get();
foreach ($registros as $registro) {
    echo "  " . json_encode($registro) . "\n";
}

echo "\n🔍 Verificando bandeiras para Cartão de Débito (ID: 25):\n";
$relacoes = DB::table('forma_pagamento_bandeiras')
    ->where('forma_pagamento_id', 25)
    ->get();

echo "📊 Relacionamentos encontrados: " . count($relacoes) . "\n";

if (count($relacoes) > 0) {
    foreach ($relacoes as $relacao) {
        // Usar o nome correto da coluna
        $bandeira_id = property_exists($relacao, 'bandeira_id') ? $relacao->bandeira_id : $relacao->forma_pag_bandeira_id;

        $bandeira = DB::table('forma_pag_bandeiras')
            ->where('id', $bandeira_id)
            ->first();
        echo "  - Bandeira ID: {$bandeira_id} - {$bandeira->nome}\n";
    }
}
