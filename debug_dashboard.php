<?php
// Debug do dashboard controller
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔍 Debug do Dashboard</h2>\n";

    // Testar a query que está falhando
    echo "<h3>1. Testando admin_dashboard_stats:</h3>\n";
    try {
        $stmt = $pdo->query("SELECT * FROM admin_dashboard_stats");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stats) {
            echo "✅ Query funcionou!<br>\n";
            echo "<pre>" . print_r($stats, true) . "</pre>\n";
        } else {
            echo "❌ Query retornou vazio<br>\n";
        }
    } catch (Exception $e) {
        echo "❌ Erro na query: " . $e->getMessage() . "<br>\n";
    }

    // Verificar se as views existem
    echo "<h3>2. Verificando Views:</h3>\n";
    $views = ['merchants', 'merchant_subscriptions', 'affiliates', 'payment_transactions'];

    foreach ($views as $view) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $view");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ $view ({$result['count']} registros)<br>\n";
        } catch (Exception $e) {
            echo "❌ $view - Erro: " . $e->getMessage() . "<br>\n";
        }
    }

    // Testar cada parte da query admin_dashboard_stats
    echo "<h3>3. Testando Partes da Query:</h3>\n";

    $queries = [
        'total_merchants' => "SELECT COUNT(*) as total FROM merchants WHERE status = 'active'",
        'active_subscriptions' => "SELECT COUNT(*) as total FROM merchant_subscriptions WHERE status = 'active'",
        'total_affiliates' => "SELECT COUNT(*) as total FROM affiliates WHERE status = 'approved'",
        'transactions_last_30_days' => "SELECT COUNT(*) as total FROM payment_transactions WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
    ];

    foreach ($queries as $name => $query) {
        try {
            $stmt = $pdo->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ $name: {$result['total']}<br>\n";
        } catch (Exception $e) {
            echo "❌ $name - Erro: " . $e->getMessage() . "<br>\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
}
