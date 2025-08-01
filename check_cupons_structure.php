<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    echo "=== ESTRUTURA DA TABELA fidelidade_cupons ===\n";
    $stmt = $pdo->query('DESCRIBE fidelidade_cupons');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "Coluna: {$column['Field']} - Tipo: {$column['Type']} - Null: {$column['Null']} - Default: {$column['Default']}\n";
    }

    echo "\n=== DADOS DA TABELA fidelidade_cupons ===\n";
    $stmt = $pdo->query('SELECT * FROM fidelidade_cupons LIMIT 5');
    $cupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cupons as $cupom) {
        echo "ID: {$cupom['id']}, Código: {$cupom['codigo']}, Nome: {$cupom['nome']}, Status: {$cupom['status']}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
