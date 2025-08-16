<?php

echo "=== TESTE DE CONEXÃO COM BANCO ===\n\n";

$configs = [
    ['host' => 'localhost', 'port' => '3306', 'username' => 'root', 'password' => ''],
    ['host' => '127.0.0.1', 'port' => '3306', 'username' => 'root', 'password' => ''],
    ['host' => 'localhost', 'port' => '3306', 'username' => 'root', 'password' => 'root'],
    ['host' => 'localhost', 'port' => '3307', 'username' => 'root', 'password' => ''],
];

foreach ($configs as $i => $config) {
    echo "Testando configuração " . ($i + 1) . ": {$config['host']}:{$config['port']} (user: {$config['username']}, pass: " . 
         ($config['password'] ? 'com senha' : 'sem senha') . ")\n";
    
    try {
        $pdo = new PDO("mysql:host={$config['host']};port={$config['port']};charset=utf8mb4", 
                      $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "  ✓ CONECTADO COM SUCESSO!\n";
        
        // Listar bancos disponíveis
        $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
        echo "  Bancos disponíveis: " . implode(', ', $databases) . "\n";
        
        // Verificar se marketplace ou meufinanceiro existem
        $targetDbs = array_intersect(['marketplace', 'meufinanceiro'], $databases);
        if ($targetDbs) {
            echo "  🎯 Bancos do projeto encontrados: " . implode(', ', $targetDbs) . "\n";
        }
        
        $pdo = null;
        break;
        
    } catch (PDOException $e) {
        echo "  ❌ Erro: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "\n=== VERIFICANDO SERVIÇOS ===\n";

// Verificar se o MySQL está rodando
$output = [];
exec('netstat -an | findstr :3306', $output);
if ($output) {
    echo "✓ MySQL parece estar rodando na porta 3306\n";
    foreach ($output as $line) {
        echo "  $line\n";
    }
} else {
    echo "❌ MySQL não parece estar rodando na porta 3306\n";
}

echo "\n";

// Verificar processos do MySQL
$output = [];
exec('tasklist | findstr mysql', $output);
if ($output) {
    echo "✓ Processos MySQL encontrados:\n";
    foreach ($output as $line) {
        echo "  $line\n";
    }
} else {
    echo "❌ Nenhum processo MySQL encontrado\n";
}

?>
