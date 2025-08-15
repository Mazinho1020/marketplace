<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

echo "Testando lancamento 377..." . PHP_EOL;

try {
    $lancamento = App\Models\Financial\LancamentoFinanceiro::find(377);
    if ($lancamento) {
        echo "Encontrado: " . $lancamento->descricao . PHP_EOL;
        echo "Valor: " . $lancamento->valor . PHP_EOL;
        echo "Empresa ID: " . $lancamento->empresa_id . PHP_EOL;
    } else {
        echo "Não encontrado" . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . PHP_EOL;
}
