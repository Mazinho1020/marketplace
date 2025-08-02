<?php
// Script para verificar dados de fidelidade na tabela funforcli

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICAÇÃO DOS DADOS DE FIDELIDADE ===\n\n";

// Verificar estrutura da tabela
echo "1. Verificando estrutura da tabela funforcli:\n";
$columns = DB::select("SHOW COLUMNS FROM funforcli");
$fidelidadeColumns = array_filter($columns, function ($col) {
    return in_array($col->Field, ['pontos_acumulados', 'nivel_fidelidade', 'saldo_disponivel', 'cashback_acumulado', 'programa_fidelidade_ativo']);
});

if (count($fidelidadeColumns) > 0) {
    echo "✅ Colunas de fidelidade encontradas:\n";
    foreach ($fidelidadeColumns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }
} else {
    echo "❌ Colunas de fidelidade não encontradas!\n";
}

echo "\n2. Verificando clientes cadastrados:\n";
$clientes = DB::table('funforcli')
    ->select('id', 'nome', 'sobrenome', 'tipo', 'ativo', 'pontos_acumulados', 'nivel_fidelidade', 'saldo_disponivel')
    ->where(function ($query) {
        $query->where('tipo', 'cliente')->orWhere('tipo', 'funcionario');
    })
    ->limit(10)
    ->get();

if ($clientes->count() > 0) {
    echo "✅ Clientes encontrados ({$clientes->count()}):\n";
    foreach ($clientes as $cliente) {
        echo "   - ID: {$cliente->id} | {$cliente->nome} {$cliente->sobrenome} | Tipo: {$cliente->tipo} | Pontos: {$cliente->pontos_acumulados} | Nível: {$cliente->nivel_fidelidade} | Saldo: R$ {$cliente->saldo_disponivel}\n";
    }
} else {
    echo "❌ Nenhum cliente encontrado!\n";
}

echo "\n3. Estatísticas gerais:\n";
$totalClientes = DB::table('funforcli')->where(function ($query) {
    $query->where('tipo', 'cliente')->orWhere('tipo', 'funcionario');
})->count();

$clientesAtivos = DB::table('funforcli')->where('ativo', 1)->where(function ($query) {
    $query->where('tipo', 'cliente')->orWhere('tipo', 'funcionario');
})->count();

$totalPontos = DB::table('funforcli')->sum('pontos_acumulados');
$totalSaldo = DB::table('funforcli')->sum('saldo_disponivel');

echo "   - Total de clientes/funcionários: {$totalClientes}\n";
echo "   - Clientes ativos: {$clientesAtivos}\n";
echo "   - Total de pontos no sistema: {$totalPontos}\n";
echo "   - Total de saldo disponível: R$ {$totalSaldo}\n";

echo "\n=== VERIFICAÇÃO CONCLUÍDA ===\n";
