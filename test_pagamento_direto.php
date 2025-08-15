<?php

require_once 'vendor/autoload.php';

use App\Models\Financial\LancamentoFinanceiro;
use App\Services\Financial\ContasPagarService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Teste direto de pagamento - Lançamento ID 377\n\n";

try {
    // 1. Verificar se o lançamento existe
    $lancamento = LancamentoFinanceiro::find(377);
    if (!$lancamento) {
        echo "❌ Lançamento 377 não encontrado\n";
        exit;
    }

    echo "✅ Lançamento encontrado: {$lancamento->descricao}\n";
    echo "   Valor final: R$ " . number_format($lancamento->valor_final, 2, ',', '.') . "\n";

    // 2. Calcular saldo devedor
    $valorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    $saldoDevedor = $lancamento->valor_final - $valorPago;
    echo "   Valor pago: R$ " . number_format($valorPago, 2, ',', '.') . "\n";
    echo "   Saldo devedor: R$ " . number_format($saldoDevedor, 2, ',', '.') . "\n\n";

    // 3. Testar instanciação do Service
    echo "🔧 Testando instanciação do ContasPagarService...\n";
    $service = new ContasPagarService();
    echo "✅ Service instanciado com sucesso\n\n";

    // 4. Preparar dados de pagamento reais
    $dadosPagamento = [
        'forma_pagamento_id' => 6,  // PIX conforme o log do frontend
        'bandeira_id' => null,
        'conta_bancaria_id' => 1,
        'valor' => 300.00,
        'valor_principal' => 300.00,
        'valor_juros' => 0,
        'valor_multa' => 0,
        'valor_desconto' => 0,
        'data_pagamento' => '2025-08-14',
        'data_compensacao' => null,
        'observacao' => 'Teste de pagamento via frontend',
        'comprovante_pagamento' => null,
        'usuario_id' => 1,
        'taxa' => 0,
        'valor_taxa' => 0,
        'referencia_externa' => null,
    ];

    echo "💳 Dados do pagamento:\n";
    echo "   Forma de pagamento: {$dadosPagamento['forma_pagamento_id']}\n";
    echo "   Valor: R$ " . number_format($dadosPagamento['valor'], 2, ',', '.') . "\n\n";

    // 5. Testar validação
    echo "🔍 Testando validação...\n";
    if ($dadosPagamento['valor'] > ($saldoDevedor + 0.01)) {
        echo "❌ Valor do pagamento maior que saldo devedor\n";
        echo "   Valor pagamento: {$dadosPagamento['valor']}\n";
        echo "   Saldo + tolerância: " . ($saldoDevedor + 0.01) . "\n";
        exit;
    }
    echo "✅ Validação passou\n\n";

    // 6. Verificar se existem as tabelas necessárias
    echo "🗄️ Verificando estrutura do banco...\n";

    // Verificar formas_pagamento
    $formaPagamento = \Illuminate\Support\Facades\DB::table('formas_pagamento')->where('id', 6)->first();
    if (!$formaPagamento) {
        echo "❌ Forma de pagamento ID 6 não encontrada\n";
        exit;
    }
    echo "✅ Forma de pagamento encontrada: {$formaPagamento->nome}\n";

    // 7. Executar pagamento
    echo "\n💰 Executando pagamento...\n";

    \Illuminate\Support\Facades\DB::beginTransaction();

    $pagamento = $service->pagar($lancamento->id, $dadosPagamento);

    echo "✅ Pagamento criado com sucesso!\n";
    echo "   ID: {$pagamento->id}\n";
    echo "   Valor: R$ " . number_format($pagamento->valor, 2, ',', '.') . "\n";
    echo "   Status: {$pagamento->status_pagamento}\n";
    echo "   Data: {$pagamento->data_pagamento}\n";

    // Verificar atualização do lançamento
    $lancamento->refresh();
    echo "\n📊 Status atualizado do lançamento:\n";
    echo "   Situação: {$lancamento->situacao_financeira->value}\n";

    $novoValorPago = $lancamento->pagamentos()->where('status_pagamento', 'confirmado')->sum('valor');
    $novoSaldoDevedor = $lancamento->valor_final - $novoValorPago;
    echo "   Novo valor pago: R$ " . number_format($novoValorPago, 2, ',', '.') . "\n";
    echo "   Novo saldo devedor: R$ " . number_format($novoSaldoDevedor, 2, ',', '.') . "\n";

    \Illuminate\Support\Facades\DB::rollBack();
    echo "\n🔄 Transação revertida (teste)\n";
    echo "\n✅ Teste concluído com sucesso!\n";
} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "❌ Erro durante o teste:\n";
    echo "   Tipo: " . get_class($e) . "\n";
    echo "   Mensagem: {$e->getMessage()}\n";
    echo "   Arquivo: {$e->getFile()}:{$e->getLine()}\n";
    echo "\n📋 Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
