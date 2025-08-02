<?php
// Script simplificado para criar views usando suas tabelas existentes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conectar ao banco de dados
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro'; // Ajuste se necessário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🚀 Criando Views para Sistema Admin</h2>\n";

    // Ler arquivo SQL simplificado
    $sql = file_get_contents('simple_views_setup.sql');

    if (!$sql) {
        throw new Exception("Arquivo simple_views_setup.sql não encontrado");
    }

    // Executar cada query separadamente
    $queries = preg_split('/;\s*\n/', $sql);
    $executed = 0;
    $errors = 0;

    foreach ($queries as $query) {
        $query = trim($query);

        // Pular comentários e linhas vazias
        if (empty($query) || strpos($query, '--') === 0) {
            continue;
        }

        try {
            $pdo->exec($query);
            $executed++;

            // Extrair nome da view
            if (preg_match('/CREATE OR REPLACE VIEW\s+(\w+)/', $query, $matches)) {
                echo "✅ View '{$matches[1]}' criada com sucesso<br>\n";
            } else {
                echo "✅ Query executada com sucesso<br>\n";
            }
        } catch (PDOException $e) {
            $errors++;
            echo "❌ Erro: " . $e->getMessage() . "<br>\n";
        }
    }

    echo "<br><h3>📊 Resultado Final:</h3>\n";
    echo "✅ Queries executadas: $executed<br>\n";
    echo "❌ Erros: $errors<br>\n";

    if ($errors == 0) {
        echo "<br><h3>🎉 Views criadas com sucesso!</h3>\n";

        // Testar views
        echo "<br><h3>🔍 Testando Views:</h3>\n";

        $test_queries = [
            'admin_dashboard_stats' => 'SELECT * FROM admin_dashboard_stats',
            'merchant_stats' => 'SELECT COUNT(*) as total FROM merchant_stats',
            'affiliate_stats' => 'SELECT COUNT(*) as total FROM affiliate_stats'
        ];

        foreach ($test_queries as $view => $query) {
            try {
                $stmt = $pdo->query($query);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ View '$view' funcionando - ";
                if ($view == 'admin_dashboard_stats') {
                    echo "Merchants: {$result['total_merchants']}, Afiliados: {$result['total_affiliates']}<br>\n";
                } else {
                    echo "Total: {$result['total']}<br>\n";
                }
            } catch (PDOException $e) {
                echo "❌ Erro na view '$view': " . $e->getMessage() . "<br>\n";
            }
        }

        echo "<br><h3>🔗 Sistema Pronto!</h3>\n";
        echo "<p><strong>Acesse o admin:</strong></p>\n";
        echo "<p>🎯 <a href='http://127.0.0.1:8000/admin' target='_blank'>Dashboard Admin Laravel</a></p>\n";
        echo "<p>📋 <a href='index.php' target='_blank'>Menu Principal</a></p>\n";
        echo "<p>⚡ <a href='menu.php' target='_blank'>Menu Rápido</a></p>\n";

        echo "<br><p><em>As views estão configuradas para trabalhar com suas tabelas 'empresas' e 'funforcli' existentes!</em></p>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
