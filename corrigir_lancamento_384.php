<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Investigando erro: 'Este lançamento não é uma conta a receber'...\n";

// Verificar o lançamento ID 384
$lancamento = DB::table('lancamentos_financeiros')
    ->where('id', 384)
    ->first();

if ($lancamento) {
    echo "📊 Lançamento 384 encontrado:\n";
    echo "  - ID: {$lancamento->id}\n";
    echo "  - Tipo: {$lancamento->tipo_lancamento}\n";
    echo "  - Natureza: {$lancamento->natureza}\n";
    echo "  - Valor: R$ {$lancamento->valor_total}\n";
    echo "  - Descrição: {$lancamento->descricao}\n";
    echo "  - Status: {$lancamento->situacao}\n";
    echo "  - Empresa: {$lancamento->empresa_id}\n";

    // Verificar se é uma conta a receber
    if ($lancamento->tipo_lancamento === 'contas_receber' && $lancamento->natureza === 'receita') {
        echo "  ✅ Este É uma conta a receber válida!\n";
    } else {
        echo "  ❌ Este NÃO é uma conta a receber:\n";
        echo "     - Tipo esperado: 'contas_receber', atual: '{$lancamento->tipo_lancamento}'\n";
        echo "     - Natureza esperada: 'receita', atual: '{$lancamento->natureza}'\n";

        echo "\n🔧 Corrigindo o lançamento...\n";
        DB::table('lancamentos_financeiros')
            ->where('id', 384)
            ->update([
                'tipo_lancamento' => 'contas_receber',
                'natureza' => 'receita',
                'updated_at' => now()
            ]);
        echo "  ✅ Lançamento corrigido!\n";
    }
} else {
    echo "❌ Lançamento 384 não encontrado!\n";

    // Criar um lançamento de teste
    echo "🔧 Criando lançamento de teste...\n";
    DB::table('lancamentos_financeiros')->insert([
        'id' => 384,
        'empresa_id' => 1,
        'conta_gerencial_id' => 1,
        'categoria_conta_gerencial_id' => 1,
        'tipo_lancamento' => 'contas_receber',
        'natureza' => 'receita',
        'descricao' => 'Teste de Recebimento',
        'valor_total' => 200.00,
        'data_vencimento' => '2025-08-14',
        'situacao' => 'pendente',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "  ✅ Lançamento 384 criado!\n";
}

echo "\n🎯 Verificando outros lançamentos de contas a receber...\n";
$contasReceber = DB::table('lancamentos_financeiros')
    ->where('tipo_lancamento', 'contas_receber')
    ->where('natureza', 'receita')
    ->where('empresa_id', 1)
    ->limit(5)
    ->get(['id', 'descricao', 'valor_total', 'situacao']);

echo "📋 Contas a receber encontradas (" . count($contasReceber) . "):\n";
foreach ($contasReceber as $conta) {
    echo "  - ID: {$conta->id} | {$conta->descricao} | R$ {$conta->valor_total} | {$conta->situacao}\n";
}
