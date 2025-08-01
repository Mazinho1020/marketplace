<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');

    echo "=== TABELAS DO SISTEMA DE FIDELIDADE ===\n\n";

    // Buscar tabelas relacionadas a fidelidade
    $result = $pdo->query("SHOW TABLES LIKE '%fidelidade%'");
    echo "Tabelas com 'fidelidade' no nome:\n";
    while ($table = $result->fetch()) {
        echo "- " . $table[0] . "\n";
    }

    echo "\n";

    // Buscar outras tabelas relacionadas
    $patterns = ['%cliente%', '%comerciante%', '%pontos%', '%premio%', '%cashback%'];

    foreach ($patterns as $pattern) {
        $result = $pdo->query("SHOW TABLES LIKE '$pattern'");
        $tables = $result->fetchAll();
        if (count($tables) > 0) {
            echo "Tabelas com '" . str_replace('%', '', $pattern) . "' no nome:\n";
            foreach ($tables as $table) {
                echo "- " . $table[0] . "\n";
            }
            echo "\n";
        }
    }

    // Mostrar todas as tabelas para identificar outras relacionadas
    echo "=== TODAS AS TABELAS DO BANCO ===\n";
    $result = $pdo->query("SHOW TABLES");
    while ($table = $result->fetch()) {
        echo "- " . $table[0] . "\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
