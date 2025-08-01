<?php

require_once __DIR__ . '/vendor/autoload.php';

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== TESTE DE CONFIGURAÇÃO DE BANCO ===\n";

// Carregar configuração diretamente
$databaseConfig = require __DIR__ . '/config/database.php';

echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "Configuração MySQL detectada:\n";
echo "  Host: " . $databaseConfig['connections']['mysql']['host'] . "\n";
echo "  Database: " . $databaseConfig['connections']['mysql']['database'] . "\n";
echo "  Username: " . $databaseConfig['connections']['mysql']['username'] . "\n";

// Testar conexão
try {
    $config = $databaseConfig['connections']['mysql'];
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);

    echo "✅ Conexão bem-sucedida!\n";
    echo "Banco conectado: " . $pdo->query('SELECT DATABASE()')->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
