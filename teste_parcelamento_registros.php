<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ANÁLISE DOS REGISTROS DE LANÇAMENTOS ===\n\n";

echo "Total de registros: " . DB::table('lancamentos')->count() . "\n";
echo "Registros com parcelamento: " . DB::table('lancamentos')->whereNotNull('grupo_parcelas')->count() . "\n";
echo "Registros com total_parcelas > 1: " . DB::table('lancamentos')->where('total_parcelas', '>', 1)->count() . "\n\n";

// Verificar registros recentes
echo "=== ÚLTIMOS 5 REGISTROS ===\n";
$ultimos = DB::table('lancamentos')
    ->select('id', 'descricao', 'total_parcelas', 'parcela_atual', 'grupo_parcelas', 'created_at')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($ultimos as $registro) {
    echo "ID: {$registro->id} | Descrição: {$registro->descricao} | Parcelas: {$registro->total_parcelas} | Atual: {$registro->parcela_atual} | Grupo: " . substr($registro->grupo_parcelas ?? 'N/A', -8) . "\n";
}

// Verificar se há grupos com múltiplas parcelas
echo "\n=== GRUPOS DE PARCELAMENTO ===\n";
$grupos = DB::table('lancamentos')
    ->select('grupo_parcelas', DB::raw('COUNT(*) as total'))
    ->whereNotNull('grupo_parcelas')
    ->groupBy('grupo_parcelas')
    ->get();

foreach ($grupos as $grupo) {
    echo "Grupo: " . substr($grupo->grupo_parcelas, -8) . " | Total de parcelas: {$grupo->total}\n";
}
