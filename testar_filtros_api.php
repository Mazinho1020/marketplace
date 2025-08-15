<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== TESTANDO FILTROS DA API CORRIGIDA ===" . PHP_EOL;

$empresaId = 1;

echo PHP_EOL . "🔍 Formas de pagamento filtradas (empresa_id=$empresaId, tipo=recebimento, origem IN sistema,pdv):" . PHP_EOL;
$formas = DB::table('formas_pagamento')
    ->where('ativo', true)
    ->where('empresa_id', $empresaId)
    ->where('tipo', 'recebimento')
    ->whereIn('origem', ['sistema', 'pdv'])
    ->orderBy('nome')
    ->get(['id', 'nome', 'gateway_method', 'tipo', 'origem']);

foreach ($formas as $forma) {
    echo "ID: $forma->id | Nome: $forma->nome | Tipo: $forma->tipo | Origem: $forma->origem" . PHP_EOL;
}

echo PHP_EOL . "📊 Comparação ANTES vs DEPOIS dos filtros:" . PHP_EOL;
$semFiltro = DB::table('formas_pagamento')->where('ativo', true)->count();
$comFiltro = $formas->count();
echo "Antes (só ativo=true): $semFiltro registros" . PHP_EOL;
echo "Depois (com todos os filtros): $comFiltro registros" . PHP_EOL;
echo "Diferença: " . ($semFiltro - $comFiltro) . " registros filtrados" . PHP_EOL;

echo PHP_EOL . "🎯 Verificando se existem formas tipo 'pagamento' (que foram excluídas):" . PHP_EOL;
$formasPagamento = DB::table('formas_pagamento')
    ->where('ativo', true)
    ->where('empresa_id', $empresaId)
    ->where('tipo', 'pagamento')
    ->count();
echo "Formas tipo 'pagamento': $formasPagamento" . PHP_EOL;

echo PHP_EOL . "🚫 Verificando se existem formas origem 'delivery' (que foram excluídas):" . PHP_EOL;
$formasDelivery = DB::table('formas_pagamento')
    ->where('ativo', true)
    ->where('empresa_id', $empresaId)
    ->where('origem', 'delivery')
    ->count();
echo "Formas origem 'delivery': $formasDelivery" . PHP_EOL;
