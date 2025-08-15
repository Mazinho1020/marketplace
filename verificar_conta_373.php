<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;

try {
    // Buscar a conta a pagar ID 373
    $conta = LancamentoFinanceiro::find(373);

    if (!$conta) {
        echo "❌ Conta ID 373 não encontrada no banco de dados\n";
        exit;
    }

    echo "💳 CONTA A PAGAR ID 373 - DADOS COMPLETOS\n";
    echo "=====================================\n\n";

    echo "📋 INFORMAÇÕES BÁSICAS:\n";
    echo "- ID: " . $conta->id . "\n";
    echo "- Descrição: " . ($conta->descricao ?? 'Não informado') . "\n";
    echo "- Empresa ID: " . $conta->empresa_id . "\n";
    echo "- Pessoa ID: " . ($conta->pessoa_id ?? 'Não informado') . "\n";
    echo "- Natureza: " . $conta->natureza_financeira->value . "\n";
    echo "- Situação: " . $conta->situacao_financeira->value . "\n\n";

    echo "💰 VALORES FINANCEIROS:\n";
    echo "- Valor Original: R$ " . number_format($conta->valor_original, 2, ',', '.') . "\n";
    echo "- Desconto: R$ " . number_format($conta->valor_desconto ?? 0, 2, ',', '.') . "\n";
    echo "- Juros: R$ " . number_format($conta->valor_juros ?? 0, 2, ',', '.') . "\n";
    echo "- Multa: R$ " . number_format($conta->valor_multa ?? 0, 2, ',', '.') . "\n";
    echo "- Valor Total: R$ " . number_format($conta->valor_total, 2, ',', '.') . "\n";
    echo "- Valor Pago: R$ " . number_format($conta->valor_pago ?? 0, 2, ',', '.') . "\n\n";

    echo "📅 DATAS:\n";
    echo "- Data Criação: " . $conta->created_at . "\n";
    echo "- Data Vencimento: " . $conta->data_vencimento . "\n";
    echo "- Data Pagamento: " . ($conta->data_pagamento ?? 'Não pago') . "\n\n";

    echo "📊 PARCELAMENTO:\n";
    echo "- Número de Parcelas: " . ($conta->numero_parcelas ?? 1) . "\n";
    echo "- Parcela Atual: " . ($conta->numero_parcela ?? 1) . "\n";
    echo "- Referência Parcela: " . ($conta->parcela_referencia ?? 'N/A') . "\n\n";

    // Verificar se há pagamentos registrados na tabela de pagamentos
    echo "💳 VERIFICANDO PAGAMENTOS:\n";
    try {
        $pagamentos = \Illuminate\Support\Facades\DB::select("SELECT * FROM pagamentos_contas_pagar WHERE lancamento_financeiro_id = 373");

        if (count($pagamentos) > 0) {
            echo "- Encontrados " . count($pagamentos) . " pagamento(s)\n";
            foreach ($pagamentos as $pagamento) {
                echo "  Pagamento ID: " . $pagamento->id . "\n";
                echo "  Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
                echo "  Data: " . $pagamento->data_pagamento . "\n";
                echo "  Status: " . $pagamento->status . "\n\n";
            }
        } else {
            echo "- Nenhum pagamento encontrado\n\n";
        }
    } catch (Exception $e) {
        echo "- Erro ao buscar pagamentos: " . $e->getMessage() . "\n\n";
    }

    // Cálculo manual para verificar se os valores estão corretos
    echo "🧮 VERIFICAÇÃO DOS CÁLCULOS:\n";
    $valorOriginal = floatval($conta->valor_original);
    $desconto = floatval($conta->valor_desconto ?? 0);
    $juros = floatval($conta->valor_juros ?? 0);
    $multa = floatval($conta->valor_multa ?? 0);

    $valorCalculado = $valorOriginal - $desconto + $juros + $multa;

    echo "- Valor Original: R$ " . number_format($valorOriginal, 2, ',', '.') . "\n";
    echo "- Menos Desconto: R$ " . number_format($desconto, 2, ',', '.') . "\n";
    echo "- Mais Juros: R$ " . number_format($juros, 2, ',', '.') . "\n";
    echo "- Mais Multa: R$ " . number_format($multa, 2, ',', '.') . "\n";
    echo "- Total Calculado: R$ " . number_format($valorCalculado, 2, ',', '.') . "\n";
    echo "- Total no Banco: R$ " . number_format($conta->valor_total, 2, ',', '.') . "\n";

    if (abs($valorCalculado - $conta->valor_total) < 0.01) {
        echo "✅ Valores estão corretos!\n";
    } else {
        echo "❌ Divergência nos valores! Diferença: R$ " . number_format(abs($valorCalculado - $conta->valor_total), 2, ',', '.') . "\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao verificar conta: " . $e->getMessage() . "\n";
}
