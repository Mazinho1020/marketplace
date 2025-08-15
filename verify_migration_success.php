<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== VERIFICANDO DADOS MIGRADOS NA TABELA PAGAMENTOS ===\n";

    // Contar registros na tabela pagamentos por tipo
    echo "=== CONTANDO POR TIPO_ID ===\n";
    $tipos = DB::table('pagamentos')
        ->select('tipo_id', DB::raw('COUNT(*) as total'))
        ->groupBy('tipo_id')
        ->get();

    foreach ($tipos as $tipo) {
        $tipoNome = $tipo->tipo_id == 1 ? 'PAGAMENTO' : 'RECEBIMENTO';
        echo "Tipo ID {$tipo->tipo_id} ({$tipoNome}): {$tipo->total} registros\n";
    }

    echo "\n=== AMOSTRA DE RECEBIMENTOS MIGRADOS (tipo_id = 2) ===\n";
    $recebimentos = DB::table('pagamentos')
        ->where('tipo_id', 2)
        ->limit(5)
        ->get();

    foreach ($recebimentos as $recebimento) {
        echo "ID: {$recebimento->id} - Lançamento: {$recebimento->lancamento_id} - Valor: R$ {$recebimento->valor} - Data: {$recebimento->data_pagamento} - Status: {$recebimento->status_pagamento}\n";
    }

    echo "\n=== TOTAL GERAL ===\n";
    $total = DB::table('pagamentos')->count();
    echo "Total de registros na tabela pagamentos: $total\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
