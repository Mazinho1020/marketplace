<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testando modelo ContaGerencial...\n";

    $conta = new \App\Models\Financial\ContaGerencial();
    echo "✅ Modelo criado com sucesso!\n";

    echo "📋 Fillable: " . implode(', ', $conta->getFillable()) . "\n";

    echo "🔍 Testando query simples...\n";
    $total = \App\Models\Financial\ContaGerencial::count();
    echo "📊 Total de contas: $total\n";

    echo "✅ Todos os testes passaram!\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "📍 Linha: " . $e->getLine() . "\n";
    echo "📂 Arquivo: " . $e->getFile() . "\n";
}
