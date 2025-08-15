<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\Recebimento;
use App\Models\Financial\LancamentoFinanceiro;

echo "=== VERIFICANDO REGISTROS ESPECÍFICOS ===\n";

// Verificar registro 392
echo "Verificando registro 392: ";
$r392 = Recebimento::find(392);
if ($r392) {
    $lancamento392 = LancamentoFinanceiro::find($r392->lancamento_id);
    echo "Existe - Lançamento: {$r392->lancamento_id}, Empresa: " . ($lancamento392 ? $lancamento392->empresa_id : 'N/A') . "\n";
} else {
    echo "Não encontrado\n";
}

// Verificar registro 394
echo "Verificando registro 394: ";
$r394 = Recebimento::find(394);
if ($r394) {
    $lancamento394 = LancamentoFinanceiro::find($r394->lancamento_id);
    echo "Existe - Lançamento: {$r394->lancamento_id}, Empresa: " . ($lancamento394 ? $lancamento394->empresa_id : 'N/A') . "\n";
} else {
    echo "Não encontrado\n";
}

// Listar todos os recebimentos existentes para ver o que temos
echo "\nListando todos os recebimentos existentes:\n";
$todosRecebimentos = Recebimento::orderBy('id')->get();
foreach ($todosRecebimentos as $rec) {
    $lancamento = LancamentoFinanceiro::find($rec->lancamento_id);
    echo "ID: {$rec->id}, Lançamento: {$rec->lancamento_id}, Empresa: " . ($lancamento ? $lancamento->empresa_id : 'N/A') . ", Status: {$rec->status_recebimento}\n";
}

// Verificar se existem lançamentos da empresa 1
echo "\nVerificando lançamentos da empresa 1:\n";
$lancamentosEmpresa1 = LancamentoFinanceiro::where('empresa_id', 1)
    ->where('natureza_financeira', 'receber')
    ->orderBy('id')
    ->get();

foreach ($lancamentosEmpresa1 as $lanc) {
    $recebimentos = Recebimento::where('lancamento_id', $lanc->id)->get();
    echo "Lançamento ID: {$lanc->id}, Valor: {$lanc->valor}, Status: {$lanc->situacao_financeira->value}, Recebimentos: " . $recebimentos->count() . "\n";
    foreach ($recebimentos as $rec) {
        echo "  -> Recebimento ID: {$rec->id}, Valor: {$rec->valor}, Status: {$rec->status_recebimento}\n";
    }
}
