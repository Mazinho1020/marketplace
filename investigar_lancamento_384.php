<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Investigando erro: 'Este lançamento não é uma conta a receber'...\n";

// Verificar o lançamento ID 384
$lancamento = DB::table('lancamentos')
    ->where('id', 384)
    ->first();

if ($lancamento) {
    echo "📊 Lançamento 384 encontrado:\n";
    echo "  - ID: {$lancamento->id}\n";
    echo "  - Tipo: " . ($lancamento->tipo ?? 'NULL') . "\n";
    echo "  - Natureza: " . ($lancamento->natureza ?? 'NULL') . "\n";
    echo "  - Valor: R$ " . ($lancamento->valor ?? $lancamento->valor_total ?? 'NULL') . "\n";
    echo "  - Descrição: " . ($lancamento->descricao ?? 'NULL') . "\n";
    echo "  - Status: " . ($lancamento->status ?? $lancamento->situacao ?? 'NULL') . "\n";
    echo "  - Empresa: " . ($lancamento->empresa_id ?? 'NULL') . "\n";

    // Mostrar todas as colunas para entender a estrutura
    echo "\n📋 Estrutura completa do registro:\n";
    foreach ($lancamento as $campo => $valor) {
        echo "  - {$campo}: {$valor}\n";
    }
} else {
    echo "❌ Lançamento 384 não encontrado!\n";

    // Verificar se existe algum lançamento
    $total = DB::table('lancamentos')->count();
    echo "📊 Total de lançamentos na tabela: {$total}\n";

    if ($total > 0) {
        $exemplo = DB::table('lancamentos')->first();
        echo "\n📋 Exemplo de lançamento (estrutura):\n";
        foreach ($exemplo as $campo => $valor) {
            echo "  - {$campo}: {$valor}\n";
        }
    }
}

// Verificar também a tabela recebimentos
echo "\n🔍 Verificando tabela recebimentos...\n";
$recebimentos = DB::table('recebimentos')->where('lancamento_id', 384)->get();
echo "📊 Recebimentos para lançamento 384: " . count($recebimentos) . "\n";

if (count($recebimentos) > 0) {
    foreach ($recebimentos as $recebimento) {
        echo "  - Recebimento ID: {$recebimento->id} | Valor: R$ {$recebimento->valor} | Status: {$recebimento->situacao}\n";
    }
}
