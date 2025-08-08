<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tabelas existentes:\n";
    $produtoTables = [];

    foreach ($tables as $table) {
        echo "- " . $table . "\n";
        if (stripos($table, 'produto') !== false) {
            $produtoTables[] = $table;
            echo "  [TABELA DE PRODUTO ENCONTRADA]\n";
        }
    }

    if (!empty($produtoTables)) {
        echo "\n=== ESTRUTURA DAS TABELAS DE PRODUTOS ===\n";
        foreach ($produtoTables as $table) {
            echo "\nTabela: $table\n";
            $stmt = $pdo->query("DESCRIBE $table");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                echo "  - {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']}\n";
            }
        }
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
