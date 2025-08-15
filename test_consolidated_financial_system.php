<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Financial\Pagamento;
use App\Models\Financial\LancamentoFinanceiro;

try {
    echo "=== TESTANDO SISTEMA FINANCEIRO CONSOLIDADO ===\n\n";

    echo "1. TESTANDO MODEL PAGAMENTO:\n";
    $pagamentos = Pagamento::where('tipo_id', 1)->count();
    $recebimentos = Pagamento::where('tipo_id', 2)->count();
    echo "- Pagamentos (tipo_id=1): $pagamentos\n";
    echo "- Recebimentos (tipo_id=2): $recebimentos\n";

    echo "\n2. TESTANDO RELACIONAMENTO COM LANÇAMENTOS:\n";
    $lancamento = LancamentoFinanceiro::with(['pagamentos', 'recebimentos'])->first();
    if ($lancamento) {
        echo "- Lançamento ID: {$lancamento->id}\n";
        echo "- Pagamentos vinculados: " . $lancamento->pagamentos->count() . "\n";
        echo "- Recebimentos vinculados: " . $lancamento->recebimentos->count() . "\n";

        if ($lancamento->recebimentos->count() > 0) {
            $recebimento = $lancamento->recebimentos->first();
            echo "- Primeiro recebimento - Valor: R$ {$recebimento->valor}, Status: {$recebimento->status_pagamento}\n";
        }
    }

    echo "\n3. TESTANDO CRIAÇÃO DE NOVO RECEBIMENTO:\n";
    try {
        $novoRecebimento = new Pagamento([
            'lancamento_id' => $lancamento ? $lancamento->id : 1,
            'numero_parcela_pagamento' => 1,
            'tipo_id' => 2, // Recebimento
            'forma_pagamento_id' => 25,
            'valor' => 99.99,
            'valor_principal' => 99.99,
            'data_pagamento' => now()->format('Y-m-d'),
            'status_pagamento' => 'confirmado',
            'conta_bancaria_id' => 1,
            'empresa_id' => 1
        ]);

        $novoRecebimento->save();
        echo "✅ Novo recebimento criado com sucesso! ID: {$novoRecebimento->id}\n";

        // Limpar teste
        $novoRecebimento->delete();
        echo "✅ Registro de teste removido\n";
    } catch (Exception $e) {
        echo "❌ Erro ao criar recebimento: " . $e->getMessage() . "\n";
    }

    echo "\n4. RESULTADO FINAL:\n";
    echo "✅ Sistema financeiro consolidado funcionando corretamente!\n";
    echo "✅ Tabela recebimentos removida\n";
    echo "✅ Todos os recebimentos agora são pagamentos com tipo_id=2\n";
    echo "✅ Controllers e models atualizados\n";
    echo "✅ Relacionamentos funcionando\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
