<?php

/**
 * Importador de banco de dados seguro
 */

// Configuração da conexão
$host = '127.0.0.1';
$port = 3306;
$database = 'meufinanceiro';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao MySQL Docker\n";

    // Lê o arquivo SQL
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2.sql';
    $sql = file_get_contents($sqlFile);

    echo "📖 Arquivo SQL carregado (" . strlen($sql) . " caracteres)\n";

    // Remove problemas comuns - mais limpo
    $sql = str_replace([
        'DELIMITER //',
        'DELIMITER ;',
        '/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */',
        '/*!40101 SET NAMES utf8 */',
        '/*!50503 SET NAMES utf8mb4 */',
        '/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */',
        '/*!40103 SET TIME_ZONE=\'+00:00\' */',
        '/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */',
        '/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */',
        '/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */'
    ], '', $sql);

    echo "🧹 SQL limpo e preparado\n";

    // Extrai comandos SQL válidos usando regex mais robusta
    $patterns = [
        '/CREATE TABLE(?:\s+IF\s+NOT\s+EXISTS)?\s+`[^`]+`\s*\([^;]*\)[^;]*;/is',
        '/INSERT INTO\s+`[^`]+`[^;]*;/is',
        '/ALTER TABLE\s+`[^`]+`[^;]*;/is',
        '/DROP TABLE(?:\s+IF\s+EXISTS)?\s+`[^`]+`[^;]*;/is'
    ];

    $commands = [];
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $sql, $matches)) {
            $commands = array_merge($commands, $matches[0]);
        }
    }

    // Remove comandos vazios
    $commands = array_filter($commands, function ($cmd) {
        return strlen(trim($cmd)) > 10;
    });

    echo "📝 Total de comandos: " . count($commands) . "\n";

    $success = 0;
    $errors = 0;

    foreach ($commands as $index => $command) {
        if (empty($command) || strlen($command) < 10) continue;

        try {
            $pdo->exec($command);
            $success++;
            if ($success % 50 == 0) {
                echo "✅ Executados: $success comandos\n";
            }
        } catch (PDOException $e) {
            $errors++;
            if ($errors <= 10) { // Mostra apenas os primeiros 10 erros
                echo "❌ Erro no comando " . ($index + 1) . ": " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n🎉 FINALIZADO!\n";
    echo "✅ Sucessos: $success\n";
    echo "❌ Erros: $errors\n";

    // Verifica tabelas criadas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "\n📊 Tabelas criadas: " . count($tables) . "\n";
    foreach (array_slice($tables, 0, 10) as $table) {
        echo "  - $table\n";
    }
    if (count($tables) > 10) {
        echo "  ... e mais " . (count($tables) - 10) . " tabelas\n";
    }
} catch (Exception $e) {
    echo "💥 ERRO: " . $e->getMessage() . "\n";
}
