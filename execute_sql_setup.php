<?php
// Script para executar o setup do sistema de pagamento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar ao banco de dados
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro'; // Ajuste conforme necessário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🚀 Executando Setup do Sistema de Pagamento</h2>\n";

    // Ler e executar o arquivo SQL
    $sql = file_get_contents('sync_marketplace_tables.sql');

    if (!$sql) {
        throw new Exception("Não foi possível ler o arquivo sync_marketplace_tables.sql");
    }

    // Dividir as consultas por ';'
    $queries = array_filter(array_map('trim', explode(';', $sql)));

    $executed = 0;
    $errors = 0;

    foreach ($queries as $query) {
        // Pular comentários e linhas vazias
        if (empty($query) || strpos($query, '--') === 0 || strpos($query, '/*') === 0) {
            continue;
        }

        try {
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $executed++;
            echo "✅ Query executada com sucesso<br>\n";
        } catch (PDOException $e) {
            $errors++;
            echo "❌ Erro na query: " . $e->getMessage() . "<br>\n";
            echo "Query: " . substr($query, 0, 100) . "...<br>\n";
        }
    }

    echo "<br><h3>📊 Resultado:</h3>\n";
    echo "✅ Queries executadas: $executed<br>\n";
    echo "❌ Erros: $errors<br>\n";

    if ($errors == 0) {
        echo "<br><h3>🎉 Setup concluído com sucesso!</h3>\n";
        echo "<p>As views foram criadas e o sistema está pronto para uso.</p>\n";

        // Testar as views criadas
        echo "<br><h3>🔍 Testando Views Criadas:</h3>\n";

        $test_views = [
            'admin_dashboard_stats',
            'merchant_stats',
            'affiliate_stats',
            'recent_transactions',
            'monthly_revenue_chart',
            'plan_distribution',
            'top_affiliate_performers'
        ];

        foreach ($test_views as $view) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_NAME = '$view'");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result['count'] > 0) {
                    echo "✅ View '$view' criada com sucesso<br>\n";
                } else {
                    echo "❌ View '$view' não encontrada<br>\n";
                }
            } catch (PDOException $e) {
                echo "❌ Erro ao verificar view '$view': " . $e->getMessage() . "<br>\n";
            }
        }

        echo "<br><h3>🔗 Próximos Passos:</h3>\n";
        echo "<p>1. Acesse o sistema admin em: <a href='http://127.0.0.1:8000/admin' target='_blank'>http://127.0.0.1:8000/admin</a></p>\n";
        echo "<p>2. Ou use o menu principal: <a href='index.php' target='_blank'>index.php</a></p>\n";
        echo "<p>3. Menu simplificado: <a href='menu.php' target='_blank'>menu.php</a></p>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
