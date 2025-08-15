<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Corrigindo lançamento 384 - Etapa 2...\n";

// Verificar estrutura da tabela conta_bancaria
echo "📋 Verificando estrutura da tabela conta_bancaria...\n";
$columns = DB::select('DESCRIBE conta_bancaria');
echo "Colunas disponíveis:\n";
foreach ($columns as $column) {
    echo "  - {$column->Field}\n";
}

// Buscar uma conta bancária da empresa sem filtro 'ativo'
$contaBancaria = DB::table('conta_bancaria')
    ->where('empresa_id', 1)
    ->first();

if ($contaBancaria) {
    echo "\n📊 Conta bancária encontrada:\n";
    echo "  - ID: {$contaBancaria->id}\n";
    echo "  - Nome: " . ($contaBancaria->nome ?? $contaBancaria->descricao ?? 'N/A') . "\n";

    // Atualizar o lançamento
    DB::table('lancamentos')
        ->where('id', 384)
        ->update([
            'conta_bancaria_id' => $contaBancaria->id,
            'updated_at' => now()
        ]);
    echo "  ✅ Conta bancária definida no lançamento!\n";
} else {
    echo "\n⚠️ Nenhuma conta bancária encontrada. Criando uma conta padrão...\n";

    // Criar uma conta bancária padrão
    $contaId = DB::table('conta_bancaria')->insertGetId([
        'empresa_id' => 1,
        'nome' => 'Conta Padrão',
        'banco' => 'Banco Padrão',
        'agencia' => '0001',
        'conta' => '12345-6',
        'tipo_conta' => 'corrente',
        'saldo_atual' => 0.00,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    DB::table('lancamentos')
        ->where('id', 384)
        ->update([
            'conta_bancaria_id' => $contaId,
            'updated_at' => now()
        ]);

    echo "  ✅ Conta bancária padrão criada (ID: {$contaId}) e definida no lançamento!\n";
}

echo "\n🔍 Verificando lançamento final...\n";
$lancamento = DB::table('lancamentos')
    ->where('id', 384)
    ->first();

if ($lancamento) {
    echo "📊 Status final do lançamento 384:\n";
    echo "  - Natureza: {$lancamento->natureza_financeira}\n";
    echo "  - Valor: R$ {$lancamento->valor}\n";
    echo "  - Valor Final: R$ " . ($lancamento->valor_final ?? 'NULL') . "\n";
    echo "  - Situação: {$lancamento->situacao_financeira}\n";
    echo "  - Conta Bancária ID: " . ($lancamento->conta_bancaria_id ?? 'NULL') . "\n";
    echo "  - Empresa ID: {$lancamento->empresa_id}\n";

    // Verificar se todos os campos necessários estão preenchidos
    $camposOk = true;
    $camposNecessarios = [
        'natureza_financeira' => $lancamento->natureza_financeira === 'receber',
        'valor_final' => !empty($lancamento->valor_final),
        'conta_bancaria_id' => !empty($lancamento->conta_bancaria_id),
        'empresa_id' => !empty($lancamento->empresa_id)
    ];

    foreach ($camposNecessarios as $campo => $ok) {
        if ($ok) {
            echo "  ✅ {$campo}: OK\n";
        } else {
            echo "  ❌ {$campo}: PROBLEMA\n";
            $camposOk = false;
        }
    }

    if ($camposOk) {
        echo "\n🎉 Lançamento 384 está completamente configurado para recebimentos!\n";
        echo "👍 Agora você pode tentar processar o recebimento novamente na interface web.\n";
    } else {
        echo "\n⚠️ Ainda há campos que precisam ser corrigidos.\n";
    }
}
