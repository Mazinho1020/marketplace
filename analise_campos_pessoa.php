<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ESTRUTURA DOS CAMPOS PESSOA/CLIENTE ===\n";

$columns = DB::select('DESCRIBE lancamentos');
foreach ($columns as $column) {
    if (strpos($column->Field, 'cliente') !== false || strpos($column->Field, 'pessoa') !== false || strpos($column->Field, 'funcionario') !== false) {
        echo sprintf("%-20s | %-15s | %s\n", $column->Field, $column->Type, $column->Comment ?? 'Sem comentário');
    }
}

echo "\n=== DADOS DE EXEMPLO DOS ÚLTIMOS REGISTROS ===\n";
$exemplos = DB::table('lancamentos')
    ->select('id', 'cliente_id', 'pessoa_id', 'pessoa_tipo', 'funcionario_id', 'descricao')
    ->orderBy('id', 'desc')
    ->limit(5)
    ->get();

foreach ($exemplos as $exemplo) {
    echo sprintf(
        "ID: %d | cliente_id: %s | pessoa_id: %s | pessoa_tipo: %s | funcionario_id: %s | desc: %s\n",
        $exemplo->id,
        $exemplo->cliente_id ?? 'null',
        $exemplo->pessoa_id ?? 'null',
        $exemplo->pessoa_tipo ?? 'null',
        $exemplo->funcionario_id ?? 'null',
        substr($exemplo->descricao, 0, 30)
    );
}

echo "\n=== ANÁLISE DA LÓGICA ATUAL ===\n";
echo "- cliente_id: Parece ser usado para compatibilidade com sistema antigo\n";
echo "- pessoa_id: Campo principal para identificar a pessoa (cliente/fornecedor)\n";
echo "- pessoa_tipo: Define se é 'cliente', 'fornecedor' ou 'funcionario'\n";
echo "- funcionario_id: Específico para funcionários\n";

echo "\n=== RECOMENDAÇÃO ===\n";
echo "✅ CORRETO: Usar apenas pessoa_id + pessoa_tipo\n";
echo "❌ REDUNDANTE: Preencher cliente_id com mesmo valor de pessoa_id\n";
