<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;

echo "=== TESTE DIRETO DE EXCLUSÃO ===\n\n";

try {
    // Testar exclusão do registro 390 que criamos
    $empresaId = 1;
    $id = 390;

    echo "Buscando registro $id da empresa $empresaId...\n";

    $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
        ->where('id', $id)
        ->where('natureza_financeira', NaturezaFinanceiraEnum::RECEBER)
        ->first();

    if (!$contaReceber) {
        echo "❌ Registro não encontrado!\n";

        // Tentar sem filtro de natureza
        $contaReceber = LancamentoFinanceiro::where('empresa_id', $empresaId)
            ->where('id', $id)
            ->first();

        if ($contaReceber) {
            echo "✅ Registro existe, mas com natureza: {$contaReceber->natureza_financeira}\n";
            echo "✅ Situação: {$contaReceber->situacao_financeira}\n";
        }
        exit;
    }

    echo "✅ Registro encontrado!\n";
    echo "   Descrição: {$contaReceber->descricao}\n";
    echo "   Situação: {$contaReceber->situacao_financeira->value}\n";

    // Verificar se não é pago
    if ($contaReceber->situacao_financeira === SituacaoFinanceiraEnum::PAGO) {
        echo "❌ Não é possível excluir - conta já foi recebida\n";
        exit;
    }

    echo "✅ Pode ser excluído\n";

    // Tentar exclusão
    $deleted = $contaReceber->delete();

    if ($deleted) {
        echo "✅ Registro excluído com sucesso!\n";
    } else {
        echo "❌ Falha na exclusão\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
