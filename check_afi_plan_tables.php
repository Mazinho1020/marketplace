<?php
// Verificar se as tabelas afi_plan_ existem no banco
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔍 Verificando Tabelas afi_plan_</h2>\n";

    $afi_tables = [
        'afi_plan_configuracoes',
        'afi_plan_gateways',
        'afi_plan_planos',
        'afi_plan_transacoes',
        'afi_plan_assinaturas',
        'afi_plan_vendas'
    ];

    $missing_tables = [];

    foreach ($afi_tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count_stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "✅ <strong>$table</strong> - Existe ($count registros)<br>\n";
        } else {
            echo "❌ <strong>$table</strong> - NÃO EXISTE<br>\n";
            $missing_tables[] = $table;
        }
    }

    // Verificar views atuais
    echo "<br><h2>👁️ Verificando Views</h2>\n";
    $views = ['merchants', 'payment_transactions', 'affiliates', 'admin_dashboard_stats'];

    foreach ($views as $view) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$view'");
        if ($stmt->rowCount() > 0) {
            echo "✅ <strong>$view</strong> - Existe<br>\n";
        } else {
            echo "❌ <strong>$view</strong> - NÃO EXISTE<br>\n";
        }
    }

    if (!empty($missing_tables)) {
        echo "<br><h2>🔧 Tabelas Faltando</h2>\n";
        echo "<p>As seguintes tabelas precisam ser criadas:</p>\n";
        foreach ($missing_tables as $table) {
            echo "- $table<br>\n";
        }
        echo "<p><strong>Solução:</strong> Vou criar as tabelas básicas para o sistema funcionar.</p>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
