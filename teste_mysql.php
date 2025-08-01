<!DOCTYPE html>
<html>

<head>
    <title>Teste de Conexão Simples</title>
</head>

<body>
    <h1>Teste de Conexão MySQL</h1>

    <?php
    try {
        echo "<h2>1. Teste de Conexão PDO</h2>";

        $host = '127.0.0.1';
        $port = 3306;
        $dbname = 'meufinanceiro';
        $username = 'root';
        $password = '';

        echo "Tentando conectar em: {$host}:{$port} -> {$dbname}<br>";

        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname}", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ Conexão PDO bem-sucedida!<br>";

        // Verificar nome do banco
        $stmt = $pdo->query('SELECT DATABASE()');
        $currentDb = $stmt->fetchColumn();
        echo "Banco atual: <strong>{$currentDb}</strong><br>";

        // Listar algumas tabelas
        echo "<h3>Tabelas encontradas:</h3>";
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach (array_slice($tables, 0, 10) as $table) {
            echo "- {$table}<br>";
        }

        if (count($tables) > 10) {
            echo "... e mais " . (count($tables) - 10) . " tabelas<br>";
        }

        // Verificar tabela empresa_usuarios
        if (in_array('empresa_usuarios', $tables)) {
            echo "<h3>Tabela empresa_usuarios:</h3>";
            $stmt = $pdo->query('SELECT COUNT(*) FROM empresa_usuarios');
            $count = $stmt->fetchColumn();
            echo "Total de registros: {$count}<br>";

            if ($count > 0) {
                $stmt = $pdo->query('SELECT email, empresa_id FROM empresa_usuarios LIMIT 5');
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "Primeiros usuários:<br>";
                foreach ($users as $user) {
                    echo "- {$user['email']} (Empresa: {$user['empresa_id']})<br>";
                }
            }
        } else {
            echo "❌ Tabela 'empresa_usuarios' não encontrada!<br>";
        }
    } catch (Exception $e) {
        echo "❌ Erro: " . $e->getMessage() . "<br>";
        echo "Código: " . $e->getCode() . "<br>";
    }
    ?>

    <hr>
    <h2>2. Informações do Sistema</h2>
    <?php
    echo "PHP Version: " . phpversion() . "<br>";
    echo "MySQL extension: " . (extension_loaded('mysql') ? 'Sim' : 'Não') . "<br>";
    echo "MySQLi extension: " . (extension_loaded('mysqli') ? 'Sim' : 'Não') . "<br>";
    echo "PDO MySQL extension: " . (extension_loaded('pdo_mysql') ? 'Sim' : 'Não') . "<br>";
    ?>

</body>

</html>