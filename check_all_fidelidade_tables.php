<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Verificar todas as tabelas de fidelidade
$tables = [
    'programas_fidelidade',
    'cartoes_fidelidade',
    'fidelidade_carteiras',
    'fidelidade_cashback_regras',
    'fidelidade_cashback_transacoes',
    'fidelidade_creditos',
    'fidelidade_conquistas',
    'fidelidade_cliente_conquistas',
    'fidelidade_cupons',
    'fidelidade_cupons_uso'
];

echo "Verificando tabelas de fidelidade:\n";
foreach ($tables as $table) {
    try {
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if (count($exists) > 0) {
            echo "✅ $table - EXISTE\n";
        } else {
            echo "❌ $table - NÃO EXISTE\n";
        }
    } catch (Exception $e) {
        echo "❌ $table - ERRO: " . $e->getMessage() . "\n";
    }
}
