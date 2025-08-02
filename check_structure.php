<?php
// Verificar estrutura exata das tabelas principais
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>📋 Estrutura das Tabelas Principais</h2>\n";

    // Verificar tabela empresas
    echo "<h3>🏢 Tabela 'empresas':</h3>\n";
    $stmt = $pdo->query("DESCRIBE empresas");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- <strong>{$row['Field']}</strong> ({$row['Type']}) {$row['Null']} {$row['Key']}<br>\n";
    }

    echo "<br><h3>👥 Tabela 'funforcli':</h3>\n";
    $stmt = $pdo->query("DESCRIBE funforcli");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- <strong>{$row['Field']}</strong> ({$row['Type']}) {$row['Null']} {$row['Key']}<br>\n";
    }

    // Verificar dados de exemplo
    echo "<br><h3>📊 Dados de Exemplo:</h3>\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM empresas");
    $empresas = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "🏢 Total de empresas: {$empresas['total']}<br>\n";

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM funforcli");
    $funforcli = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "👥 Total de funforcli: {$funforcli['total']}<br>\n";

    // Verificar alguns registros
    echo "<br><h3>📝 Primeiros registros da tabela 'empresas':</h3>\n";
    $stmt = $pdo->query("SELECT * FROM empresas LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} - ";
        foreach ($row as $field => $value) {
            if (in_array($field, ['razao_social', 'nome_fantasia', 'email', 'ativo'])) {
                echo "$field: " . ($value ?? 'NULL') . " | ";
            }
        }
        echo "<br>\n";
    }

    echo "<br><h3>📝 Primeiros registros da tabela 'funforcli':</h3>\n";
    $stmt = $pdo->query("SELECT * FROM funforcli LIMIT 3");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} - ";
        foreach ($row as $field => $value) {
            if (in_array($field, ['nome', 'email', 'tipo', 'ativo', 'empresa_id'])) {
                echo "$field: " . ($value ?? 'NULL') . " | ";
            }
        }
        echo "<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
