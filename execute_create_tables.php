<?php
// Executar criação das tabelas afi_plan_
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>🔧 Criando Tabelas afi_plan_</h2>\n";

    // Ler e executar o SQL
    $sql = file_get_contents('create_afi_plan_tables.sql');

    // Dividir por statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $created = 0;
    $errors = 0;

    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) continue;

        try {
            $pdo->exec($statement);
            $created++;

            if (preg_match('/CREATE TABLE.*?(\w+)\s*\(/i', $statement, $matches)) {
                echo "✅ Tabela '{$matches[1]}' criada<br>\n";
            }
        } catch (Exception $e) {
            $errors++;
            echo "❌ Erro: " . $e->getMessage() . "<br>\n";
        }
    }

    echo "<br><h3>📊 Resultado:</h3>\n";
    echo "✅ Statements executados: $created<br>\n";
    echo "❌ Erros: $errors<br>\n";

    // Verificar tabelas criadas
    echo "<br><h3>🔍 Verificando Tabelas Criadas:</h3>\n";
    $tables = ['afi_plan_configuracoes', 'afi_plan_gateways', 'afi_plan_planos', 'afi_plan_assinaturas', 'afi_plan_transacoes', 'afi_plan_vendas'];

    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count_stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "✅ $table ($count registros)<br>\n";
        } else {
            echo "❌ $table - não criada<br>\n";
        }
    }

    if ($errors == 0) {
        echo "<br><h3>🎉 Tabelas criadas com sucesso!</h3>\n";
        echo "<p>Agora vou executar o script das views...</p>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
