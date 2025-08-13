<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Financial\LancamentoFinanceiro;
use App\Enums\NaturezaFinanceiraEnum;
use App\Enums\SituacaoFinanceiraEnum;

try {
    $lancamento = LancamentoFinanceiro::create([
        'empresa_id' => 1,
        'natureza_financeira' => NaturezaFinanceiraEnum::PAGAR,
        'situacao_financeira' => SituacaoFinanceiraEnum::PENDENTE,
        'descricao' => 'Teste de correção',
        'valor' => 150.00,
        'valor_original' => 150.00,
        'data_vencimento' => '2025-08-15',
        'conta_gerencial_id' => 47,
        'usuario_id' => 1,
    ]);

    echo "✅ Lançamento criado com sucesso! ID: " . $lancamento->id . "\n";
    echo "Descrição: " . $lancamento->descricao . "\n";
    echo "Valor: R$ " . number_format($lancamento->valor, 2, ',', '.') . "\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
