<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use Illuminate\Support\Facades\DB;

echo 'Testando a consulta do controller...' . PHP_EOL;

$empresaId = 1;
$id = 388;

try {
    // Testar a consulta exata do controller
    $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
        ->where('id', $id)
        ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
        ->firstOrFail();

    echo 'Consulta funcionou!' . PHP_EOL;
    echo 'ID: ' . $contaReceber->id . PHP_EOL;
    echo 'Descrição: ' . $contaReceber->descricao . PHP_EOL;
} catch (Exception $e) {
    echo 'Erro na consulta: ' . $e->getMessage() . PHP_EOL;

    // Testar consultas alternativas
    echo "\nTestando consultas alternativas:\n";

    // Sem filtro de natureza
    $sem_natureza = LancamentoFinanceiro::where('empresa_id', $empresaId)->where('id', $id)->first();
    if ($sem_natureza) {
        echo 'Sem filtro natureza: OK - Natureza atual: ' . $sem_natureza->natureza_financeira . PHP_EOL;
    }

    // Verificar enum
    echo 'Enum RECEBER: ' . NaturezaFinanceiraEnum::RECEBER->value . PHP_EOL;

    // Consulta raw
    $raw = DB::table('lancamentos')
        ->where('empresa_id', $empresaId)
        ->where('id', $id)
        ->where('natureza_financeira', 'receber')
        ->first();

    if ($raw) {
        echo 'Consulta raw funcionou!' . PHP_EOL;
    }
}
