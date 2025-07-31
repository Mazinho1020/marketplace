<?php
// Script temporário para verificar tabelas
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

    echo "Tabelas encontradas no banco meufinanceiro:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }

    echo "\nTabelas relacionadas a empresa/business:\n";
    foreach ($tables as $table) {
        if (stripos($table, 'empresa') !== false || stripos($table, 'business') !== false) {
            echo "- $table\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
