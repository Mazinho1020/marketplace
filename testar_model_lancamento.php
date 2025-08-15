<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;

echo "🔍 Testando o Model LancamentoFinanceiro e enums...\n";

try {
    $lancamento = LancamentoFinanceiro::find(384);

    if ($lancamento) {
        echo "📊 Lançamento 384 carregado:\n";
        echo "  - ID: {$lancamento->id}\n";
        echo "  - Natureza financeira (raw): " . var_export($lancamento->natureza_financeira, true) . "\n";
        echo "  - Tipo da natureza: " . gettype($lancamento->natureza_financeira) . "\n";

        // Verificar se é enum
        if (is_object($lancamento->natureza_financeira)) {
            echo "  - Classe: " . get_class($lancamento->natureza_financeira) . "\n";
            if (method_exists($lancamento->natureza_financeira, 'value')) {
                echo "  - Valor do enum: " . $lancamento->natureza_financeira->value . "\n";
            }
        }

        // Testar comparações
        echo "\n🧪 Testando comparações:\n";
        echo "  - natureza === 'receber': " . var_export($lancamento->natureza_financeira === 'receber', true) . "\n";
        echo "  - natureza == 'receber': " . var_export($lancamento->natureza_financeira == 'receber', true) . "\n";

        // Se for enum, testar com valor
        if (is_object($lancamento->natureza_financeira) && method_exists($lancamento->natureza_financeira, 'value')) {
            echo "  - natureza->value === 'receber': " . var_export($lancamento->natureza_financeira->value === 'receber', true) . "\n";
        }

        // Testar também outros campos importantes
        echo "\n📋 Outros campos importantes:\n";
        echo "  - Valor final: " . var_export($lancamento->valor_final, true) . "\n";
        echo "  - Empresa ID: {$lancamento->empresa_id}\n";
        echo "  - Situação: " . var_export($lancamento->situacao_financeira, true) . "\n";

        // Testar relacionamento recebimentos
        echo "\n💰 Testando relacionamento recebimentos:\n";
        $recebimentos = $lancamento->recebimentos;
        echo "  - Total de recebimentos: " . count($recebimentos) . "\n";

        foreach ($recebimentos as $recebimento) {
            echo "    - Recebimento ID: {$recebimento->id} | Valor: R$ {$recebimento->valor} | Status: {$recebimento->status_recebimento}\n";
        }
    } else {
        echo "❌ Lançamento 384 não encontrado usando o Model!\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao carregar o lançamento: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
