<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$lancamento = DB::table('lancamentos')->whereNotNull('grupo_parcelas')->first();
if ($lancamento) {
    echo 'ID: ' . $lancamento->id . PHP_EOL;
    echo 'Descrição: ' . $lancamento->descricao . PHP_EOL;
    echo 'Parcela: ' . $lancamento->parcela_atual . '/' . $lancamento->total_parcelas . PHP_EOL;
    echo 'Grupo: ' . $lancamento->grupo_parcelas . PHP_EOL;
    echo 'Intervalo: ' . $lancamento->intervalo_parcelas . PHP_EOL;
    echo 'URL de edição: http://127.0.0.1:8000/comerciantes/empresas/1/financeiro/contas-receber/' . $lancamento->id . '/edit' . PHP_EOL;
} else {
    echo 'Nenhum lançamento com parcelamento encontrado' . PHP_EOL;
}
