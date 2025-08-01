<?php

/**
 * Teste final da configuração de banco dinâmica
 * Este arquivo simula exatamente o que o Laravel faria
 */

echo "=== TESTE FINAL DE CONFIGURAÇÃO ===\n";

// Simular função env() do Laravel
function env($key, $default = null)
{
    static $envVars = null;

    if ($envVars === null) {
        $envVars = [];
        if (file_exists(__DIR__ . '/.env')) {
            $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (!str_contains($line, '=')) continue;

                [$key, $value] = explode('=', trim($line), 2);
                $envVars[trim($key)] = trim($value, '"');
            }
        }
    }

    return $envVars[$key] ?? $default;
}

// Carregar configuração exatamente como o Laravel faria
echo "Carregando config/database.php...\n";
$databaseConfig = require __DIR__ . '/config/database.php';

echo "\nVariáveis de ambiente:\n";
echo "  APP_ENV: " . env('APP_ENV') . "\n";
echo "  DB_HOST: " . env('DB_HOST') . "\n";
echo "  DB_DATABASE: " . env('DB_DATABASE') . "\n";

echo "\nConfiguração MySQL resultante:\n";
$mysqlConfig = $databaseConfig['connections']['mysql'];
echo "  Host: " . $mysqlConfig['host'] . "\n";
echo "  Database: " . $mysqlConfig['database'] . "\n";
echo "  Username: " . $mysqlConfig['username'] . "\n";
echo "  Password: " . (empty($mysqlConfig['password']) ? '(vazio)' : '***') . "\n";

// Testar conexão real
echo "\nTestando conexão real...\n";
try {
    $dsn = "mysql:host={$mysqlConfig['host']};port={$mysqlConfig['port']};dbname={$mysqlConfig['database']}";
    $pdo = new PDO($dsn, $mysqlConfig['username'], $mysqlConfig['password']);

    $actualDb = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "✅ Conexão bem-sucedida!\n";
    echo "✅ Banco conectado: {$actualDb}\n";

    if ($actualDb === 'meufinanceiro') {
        echo "✅ SUCESSO: Conectado ao banco LOCAL correto!\n";
    } elseif (str_contains($actualDb, 'finanp06_')) {
        echo "❌ PROBLEMA: Conectado ao banco ONLINE!\n";
    } else {
        echo "⚠️  ATENÇÃO: Conectado a banco inesperado: {$actualDb}\n";
    }

    // Verificar se as tabelas de configuração existem
    $stmt = $pdo->query("SHOW TABLES LIKE 'config_%'");
    $configTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tabelas de configuração encontradas: " . count($configTables) . "\n";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
