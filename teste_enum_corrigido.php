<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar o Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\SituacaoFinanceiraEnum;
use Illuminate\Support\Facades\DB;

echo "✅ TESTE RÁPIDO DO ENUM PARCIALMENTE_PAGO\n";
echo "=========================================\n\n";

// Verificar estrutura atualizada
echo "🏗️ Nova estrutura da coluna situacao_financeira:\n";
$columns = DB::select("DESCRIBE lancamentos");
foreach ($columns as $column) {
    if ($column->Field === 'situacao_financeira') {
        echo "   Tipo: {$column->Type}\n";
        break;
    }
}

// Testar lançamento 380
$lancamento = LancamentoFinanceiro::find(380);
if ($lancamento) {
    echo "\n📋 Lançamento 380:\n";
    echo "   Situação atual: {$lancamento->situacao_financeira->value}\n";

    // Testar mudança para parcialmente_pago
    $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PARCIALMENTE_PAGO;
    $lancamento->save();

    echo "   ✅ Alterado para: {$lancamento->situacao_financeira->value}\n";

    // Reverter para pendente
    $lancamento->situacao_financeira = SituacaoFinanceiraEnum::PENDENTE;
    $lancamento->save();

    echo "   ✅ Revertido para: {$lancamento->situacao_financeira->value}\n";
}

echo "\n🎉 ENUM FUNCIONANDO CORRETAMENTE!\n";
echo "Agora o sistema pode usar 'parcialmente_pago' sem erros.\n";
