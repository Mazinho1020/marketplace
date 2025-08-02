<?php
// Executar o script completo das views
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Executando Script das Views</h2>\n";

    // Ler o arquivo SQL principal
    $sql = file_get_contents('sync_marketplace_tables.sql');

    if (!$sql) {
        throw new Exception("Arquivo sync_marketplace_tables.sql não encontrado");
    }

    // Dividir por declarações CREATE OR REPLACE VIEW
    $view_statements = [];
    $lines = explode("\n", $sql);
    $current_statement = '';
    $in_view = false;

    foreach ($lines as $line) {
        if (strpos($line, 'CREATE OR REPLACE VIEW') !== false) {
            if ($current_statement && $in_view) {
                $view_statements[] = trim($current_statement);
            }
            $current_statement = $line;
            $in_view = true;
        } elseif ($in_view) {
            $current_statement .= "\n" . $line;
            if (trim($line) === '' && strpos($current_statement, ';') !== false) {
                $view_statements[] = trim($current_statement);
                $current_statement = '';
                $in_view = false;
            }
        }
    }

    if ($current_statement && $in_view) {
        $view_statements[] = trim($current_statement);
    }

    $created = 0;
    $errors = 0;

    foreach ($view_statements as $statement) {
        if (empty(trim($statement)) || strpos($statement, '--') === 0) continue;

        // Remover ; final se existir
        $statement = rtrim($statement, ';');

        try {
            $pdo->exec($statement);
            $created++;

            if (preg_match('/CREATE OR REPLACE VIEW\s+(\w+)/i', $statement, $matches)) {
                echo "✅ View '{$matches[1]}' criada<br>\n";
            }
        } catch (Exception $e) {
            $errors++;
            if (preg_match('/CREATE OR REPLACE VIEW\s+(\w+)/i', $statement, $matches)) {
                echo "❌ Erro na view '{$matches[1]}': " . $e->getMessage() . "<br>\n";
            } else {
                echo "❌ Erro: " . $e->getMessage() . "<br>\n";
            }
        }
    }

    echo "<br><h3>📊 Resultado:</h3>\n";
    echo "✅ Views criadas: $created<br>\n";
    echo "❌ Erros: $errors<br>\n";

    if ($created > 0) {
        echo "<br><h3>🧪 Testando Views:</h3>\n";

        $test_views = ['merchants', 'affiliates', 'payment_transactions', 'admin_dashboard_stats'];

        foreach ($test_views as $view) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $view");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ $view ({$result['count']} registros)<br>\n";
            } catch (Exception $e) {
                echo "❌ $view - Erro: " . $e->getMessage() . "<br>\n";
            }
        }

        echo "<br><h3>🎉 Sistema Totalmente Configurado!</h3>\n";
        echo "<p><a href='http://127.0.0.1:8000/admin/payments' target='_blank' style='background: #28a745; color: white; padding: 15px 25px; text-decoration: none; border-radius: 5px;'>🧪 Testar Página de Pagamentos</a></p>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
