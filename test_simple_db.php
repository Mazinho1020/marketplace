<?php

echo "=== TESTE SIMPLES DE BANCO ===\n";

try {
    // Conectar diretamente usando .env
    $host = '127.0.0.1';
    $database = 'meufinanceiro';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
    echo "✅ Conexão direta OK: {$database}\n";

    // Verificar tabelas
    $stmt = $pdo->query("SHOW TABLES LIKE 'config_%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tabelas config encontradas: " . count($tables) . "\n";

    // Verificar ambientes
    $stmt = $pdo->query("SELECT id, codigo, nome FROM config_environments");
    $environments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Ambientes:\n";
    foreach ($environments as $env) {
        echo "  - {$env['codigo']} (ID: {$env['id']})\n";
    }

    // Verificar conexões
    $stmt = $pdo->query("SELECT id, nome, ambiente_id, banco FROM config_db_connections");
    $connections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Conexões:\n";
    foreach ($connections as $conn) {
        echo "  - {$conn['nome']} (Ambiente: {$conn['ambiente_id']}, Banco: {$conn['banco']})\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
