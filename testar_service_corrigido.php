<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use App\Services\Financial\ContasReceberService;
use App\Enums\NaturezaFinanceiraEnum;

echo "🎯 Testando validação do Service após correção...\n";

try {
    $lancamento = LancamentoFinanceiro::find(384);

    if ($lancamento) {
        echo "📊 Lançamento carregado:\n";
        echo "  - ID: {$lancamento->id}\n";
        echo "  - Natureza: " . get_class($lancamento->natureza_financeira) . "\n";

        // Verificar se é o enum correto
        if ($lancamento->natureza_financeira === NaturezaFinanceiraEnum::RECEBER) {
            echo "  ✅ Natureza financeira é RECEBER (enum correto)\n";
        } else {
            echo "  ❌ Natureza financeira não é RECEBER\n";
        }

        echo "\n🧪 Testando validação do Service...\n";
        $service = new ContasReceberService();

        // Usar reflexão para acessar o método privado
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('validarRecebimento');
        $method->setAccessible(true);

        $dados = ['valor' => 200.00];

        $method->invoke($service, $lancamento, $dados);
        echo "  ✅ Validação passou! O lançamento é uma conta a receber válida.\n";

        echo "\n🚀 Testando método completo receber()...\n";

        $dadosRecebimento = [
            'forma_pagamento_id' => 25, // Cartão de Débito
            'bandeira_id' => 35, // Elo Débito
            'conta_bancaria_id' => 1,
            'valor' => 200.00,
            'valor_principal' => 200.00,
            'valor_juros' => 0,
            'valor_multa' => 0,
            'valor_desconto' => 0,
            'data_recebimento' => '2025-08-14',
            'data_compensacao' => null,
            'observacao' => 'Teste de recebimento via API',
            'comprovante_recebimento' => null,
            'taxa' => 1.39,
            'valor_taxa' => 2.78,
            'referencia_externa' => null,
            'usuario_id' => 1
        ];

        $recebimento = $service->receber(384, $dadosRecebimento);
        echo "  ✅ Recebimento criado com sucesso!\n";
        echo "  - ID do recebimento: {$recebimento->id}\n";
        echo "  - Valor: R$ {$recebimento->valor}\n";
        echo "  - Status: {$recebimento->status_recebimento}\n";
    } else {
        echo "❌ Lançamento 384 não encontrado!\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " Linha: " . $e->getLine() . "\n";
}
