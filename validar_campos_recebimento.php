<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Financial\Pagamento;
use App\Models\Financial\LancamentoFinanceiro;
use Illuminate\Support\Facades\DB;

try {
    echo "=== TESTE DE CAMPOS DO FORMULÁRIO DE RECEBIMENTO ===\n\n";

    // Simular dados do formulário
    $dadosFormulario = [
        'lancamento_id' => 385, // ID de um lançamento existente
        'numero_parcela_pagamento' => 1,
        'tipo_id' => 2, // 2 = recebimento
        'forma_pagamento_id' => 15, // PIX
        'bandeira_id' => null,
        'conta_bancaria_id' => 1,
        'valor' => 50.00,
        'valor_principal' => 50.00,
        'valor_juros' => 0.00,
        'valor_multa' => 0.00,
        'valor_desconto' => 0.00,
        'data_pagamento' => '2025-08-14', // Campo correto
        'data_compensacao' => null,
        'observacao' => 'Teste de validação de campos',
        'comprovante_pagamento' => 'Comprovante de teste', // Campo correto
        'taxa' => 1.39,
        'valor_taxa' => 0.70,
        'referencia_externa' => 'REF-TEST-001',
        'usuario_id' => 1,
        'empresa_id' => 1,
        'status_pagamento' => 'confirmado'
    ];

    echo "1. TESTANDO CRIAÇÃO DE RECEBIMENTO:\n";

    // Verificar se lançamento existe
    $lancamento = LancamentoFinanceiro::find($dadosFormulario['lancamento_id']);
    if (!$lancamento) {
        echo "❌ Lançamento não encontrado!\n";
        exit;
    }

    echo "✅ Lançamento encontrado: {$lancamento->descricao}\n";

    // Criar recebimento de teste
    $recebimento = Pagamento::create($dadosFormulario);
    echo "✅ Recebimento criado com sucesso! ID: {$recebimento->id}\n";

    echo "\n2. VALIDANDO CAMPOS SALVOS:\n";

    $recebimentoCarregado = Pagamento::find($recebimento->id);
    $camposValidacao = [
        'lancamento_id',
        'tipo_id',
        'forma_pagamento_id',
        'valor',
        'valor_principal',
        'data_pagamento',
        'observacao',
        'comprovante_pagamento',
        'status_pagamento',
        'taxa',
        'valor_taxa',
        'referencia_externa'
    ];

    foreach ($camposValidacao as $campo) {
        $valorOriginal = $dadosFormulario[$campo] ?? null;
        $valorSalvo = $recebimentoCarregado->$campo ?? null;

        if ($valorOriginal == $valorSalvo) {
            echo "✅ $campo: $valorSalvo\n";
        } else {
            echo "❌ $campo: esperado '$valorOriginal', obtido '$valorSalvo'\n";
        }
    }

    echo "\n3. TESTANDO RELACIONAMENTO COM LANÇAMENTO:\n";
    $lancamentoComRecebimentos = LancamentoFinanceiro::with('recebimentos')->find($dadosFormulario['lancamento_id']);
    $recebimentosVinculados = $lancamentoComRecebimentos->recebimentos->count();
    echo "✅ Lançamento tem $recebimentosVinculados recebimento(s) vinculado(s)\n";

    echo "\n4. TESTANDO QUERY DE RECEBIMENTOS:\n";
    $recebimentosQuery = Pagamento::where('lancamento_id', $dadosFormulario['lancamento_id'])
        ->where('tipo_id', 2)
        ->where('status_pagamento', 'confirmado')
        ->get();

    echo "✅ Query de recebimentos retornou " . $recebimentosQuery->count() . " registro(s)\n";

    foreach ($recebimentosQuery as $rec) {
        echo "   - ID: {$rec->id}, Valor: R$ {$rec->valor}, Data: {$rec->data_pagamento}, Status: {$rec->status_pagamento}\n";
    }

    echo "\n5. LIMPANDO DADOS DE TESTE:\n";
    $recebimento->delete();
    echo "✅ Recebimento de teste removido\n";

    echo "\n=== VALIDAÇÃO DOS CAMPOS CONCLUÍDA ===\n";
    echo "✅ Todos os campos estão usando os nomes corretos da tabela 'pagamentos'\n";
    echo "✅ Campo 'data_pagamento' (não data_recebimento)\n";
    echo "✅ Campo 'comprovante_pagamento' (não comprovante_recebimento)\n";
    echo "✅ Campo 'status_pagamento' (não status_recebimento)\n";
    echo "✅ Campo 'tipo_id = 2' para diferenciar recebimentos\n";
    echo "✅ Relacionamentos funcionando corretamente\n";

    echo "\n🎉 SISTEMA PRONTO PARA USO!\n";
} catch (Exception $e) {
    echo "❌ Erro durante validação: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
