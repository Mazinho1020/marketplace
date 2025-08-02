<?php
echo "=== IMPLEMENTANDO SISTEMA DE FIDELIDADE COMPLETO ===\n\n";

$sqlFile = __DIR__ . '/database/fidelidade_sistema_completo.sql';

if (!file_exists($sqlFile)) {
    echo "❌ Arquivo SQL não encontrado: $sqlFile\n";
    exit;
}

echo "📂 Arquivo SQL encontrado: " . basename($sqlFile) . "\n";
echo "📊 Tamanho: " . number_format(filesize($sqlFile) / 1024, 2) . " KB\n\n";

try {
    // Configurações do banco
    $host = '127.0.0.1';
    $database = 'meufinanceiro';
    $username = 'root';
    $password = '';

    echo "🔌 Conectando ao banco...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    echo "✅ Conexão estabelecida!\n\n";

    // Desabilitar verificações temporariamente
    echo "⚙️ Configurando para execução...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
    $pdo->exec("SET AUTOCOMMIT = 0");
    $pdo->exec("START TRANSACTION");

    echo "📄 Lendo arquivo SQL...\n";
    $sql = file_get_contents($sqlFile);

    // Dividir o SQL em statements individuais
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    echo "🔄 Executando " . count($statements) . " comandos SQL...\n\n";

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $index => $statement) {
        if (empty($statement) || substr(trim($statement), 0, 1) === '#') {
            continue; // Pular comentários e linhas vazias
        }

        try {
            $pdo->exec($statement);
            $successCount++;

            // Mostrar progresso para comandos importantes
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE `?([^`\s]+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'tabela';
                echo "✅ Tabela criada: $tableName\n";
            } elseif (stripos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO `?([^`\s]+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'tabela';
                echo "📝 Dados inseridos em: $tableName\n";
            } elseif (stripos($statement, 'DROP TABLE') !== false) {
                preg_match('/DROP TABLE.*`?([^`\s]+)`?/i', $statement, $matches);
                $tableName = $matches[1] ?? 'tabela';
                echo "🗑️ Tabela removida: $tableName\n";
            }
        } catch (PDOException $e) {
            $errorCount++;
            echo "❌ Erro no comando " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "💡 SQL: " . substr($statement, 0, 100) . "...\n\n";
        }
    }

    // Confirmar transação
    $pdo->exec("COMMIT");

    // Reabilitar verificações
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "\n=== RESULTADO ===\n";
    echo "✅ Comandos executados com sucesso: $successCount\n";
    echo "❌ Comandos com erro: $errorCount\n\n";

    // Verificar tabelas criadas
    echo "📋 Verificando tabelas do sistema de fidelidade:\n";
    $result = $pdo->query("SHOW TABLES LIKE 'fidelidade_%'");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "  📊 $table: $count registros\n";
    }

    echo "\n🎉 Sistema de fidelidade implementado com sucesso!\n";
    echo "🔗 Acesse: http://localhost:8000/admin/fidelidade/cupons\n\n";
} catch (PDOException $e) {
    echo "❌ Erro de conexão: " . $e->getMessage() . "\n";
    exit;
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
    exit;
}
