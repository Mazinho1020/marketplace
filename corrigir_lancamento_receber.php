<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Corrigindo lançamento 384 para ser uma conta a receber válida...\n";

// Primeiro, verificar o lançamento atual
$lancamento = DB::table('lancamentos')
    ->where('id', 384)
    ->first();

if ($lancamento) {
    echo "📊 Lançamento 384 encontrado:\n";
    echo "  - Natureza atual: {$lancamento->natureza_financeira}\n";
    echo "  - Valor: R$ {$lancamento->valor}\n";
    echo "  - Situação: {$lancamento->situacao_financeira}\n";

    // Verificar se já é 'receber'
    if ($lancamento->natureza_financeira === 'receber') {
        echo "  ✅ Natureza financeira já está correta!\n";
    } else {
        echo "  🔄 Atualizando natureza financeira para 'receber'...\n";
        DB::table('lancamentos')
            ->where('id', 384)
            ->update([
                'natureza_financeira' => 'receber',
                'updated_at' => now()
            ]);
        echo "  ✅ Natureza financeira atualizada!\n";
    }

    // Verificar se o valor_final está definido
    if (empty($lancamento->valor_final)) {
        echo "  🔄 Definindo valor_final...\n";
        DB::table('lancamentos')
            ->where('id', 384)
            ->update([
                'valor_final' => $lancamento->valor,
                'updated_at' => now()
            ]);
        echo "  ✅ Valor final definido como R$ {$lancamento->valor}!\n";
    }

    // Verificar se já tem conta_bancaria_id definida
    if (empty($lancamento->conta_bancaria_id)) {
        echo "  🔄 Definindo conta bancária padrão...\n";
        // Buscar uma conta bancária da empresa
        $contaBancaria = DB::table('conta_bancaria')
            ->where('empresa_id', $lancamento->empresa_id)
            ->where('ativo', 1)
            ->first();

        if ($contaBancaria) {
            DB::table('lancamentos')
                ->where('id', 384)
                ->update([
                    'conta_bancaria_id' => $contaBancaria->id,
                    'updated_at' => now()
                ]);
            echo "  ✅ Conta bancária definida (ID: {$contaBancaria->id})!\n";
        } else {
            echo "  ⚠️ Nenhuma conta bancária encontrada para a empresa.\n";
        }
    }
} else {
    echo "❌ Lançamento 384 não encontrado!\n";
}

echo "\n🔍 Verificando lançamento corrigido...\n";
$lancamentoCorrigido = DB::table('lancamentos')
    ->where('id', 384)
    ->first();

if ($lancamentoCorrigido) {
    echo "📊 Status após correção:\n";
    echo "  - Natureza: {$lancamentoCorrigido->natureza_financeira}\n";
    echo "  - Valor: R$ {$lancamentoCorrigido->valor}\n";
    echo "  - Valor Final: R$ " . ($lancamentoCorrigido->valor_final ?? 'NULL') . "\n";
    echo "  - Situação: {$lancamentoCorrigido->situacao_financeira}\n";
    echo "  - Conta Bancária ID: " . ($lancamentoCorrigido->conta_bancaria_id ?? 'NULL') . "\n";
    echo "  - Empresa ID: {$lancamentoCorrigido->empresa_id}\n";

    if ($lancamentoCorrigido->natureza_financeira === 'receber') {
        echo "\n✅ Lançamento 384 está pronto para receber pagamentos!\n";
    } else {
        echo "\n❌ Ainda há problemas com o lançamento.\n";
    }
}

echo "\n🎯 Testando a validação do Service...\n";
try {
    $service = new \App\Services\Financial\ContasReceberService();

    // Testar o método privado através de reflexão
    $reflection = new ReflectionClass($service);
    $method = $reflection->getMethod('validarRecebimento');
    $method->setAccessible(true);

    $lancamentoModel = \App\Models\Financial\LancamentoFinanceiro::find(384);
    $dados = ['valor' => 200.00];

    $method->invoke($service, $lancamentoModel, $dados);
    echo "  ✅ Validação do Service passou!\n";
} catch (Exception $e) {
    echo "  ❌ Erro na validação: " . $e->getMessage() . "\n";
}
