<?php
// Corrigir todas as datas no DashboardController para usar Carbon
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Verificando Estrutura das Views para Datas</h2>\n";

    // Verificar estrutura da view recent_transactions
    echo "<h3>📅 Verificando recent_transactions:</h3>\n";
    $stmt = $pdo->query("SELECT * FROM recent_transactions LIMIT 1");
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        echo "✅ Campos encontrados:<br>\n";
        foreach ($transaction as $field => $value) {
            echo "- <strong>$field</strong>: " . ($value ?? 'NULL') . " (" . gettype($value) . ")<br>\n";
        }
    } else {
        echo "❌ Nenhuma transação encontrada<br>\n";
    }

    // Verificar top merchants
    echo "<br><h3>🏢 Verificando merchant_stats:</h3>\n";
    $stmt = $pdo->query("SELECT * FROM merchant_stats LIMIT 1");
    $merchant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($merchant) {
        echo "✅ Campos encontrados:<br>\n";
        foreach ($merchant as $field => $value) {
            echo "- <strong>$field</strong>: " . ($value ?? 'NULL') . " (" . gettype($value) . ")<br>\n";
        }
    } else {
        echo "❌ Nenhum merchant encontrado<br>\n";
    }

    // Verificar subscription plans
    echo "<br><h3>📋 Verificando subscription_plans:</h3>\n";
    $stmt = $pdo->query("SELECT * FROM subscription_plans LIMIT 1");
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($plan) {
        echo "✅ Campos encontrados:<br>\n";
        foreach ($plan as $field => $value) {
            echo "- <strong>$field</strong>: " . ($value ?? 'NULL') . " (" . gettype($value) . ")<br>\n";
        }
    } else {
        echo "❌ Nenhum plano encontrado<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
