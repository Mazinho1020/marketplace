<?php
// Script para verificar estrutura das tabelas
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', '');

    echo "Estrutura da tabela 'empresas':\n";
    $columns = $pdo->query('DESCRIBE empresas')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }

    echo "\nEstrutura da tabela 'funforcli' (clientes):\n";
    $columns = $pdo->query('DESCRIBE funforcli')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }

    echo "\nEstrutura da tabela 'empresa_usuarios' (usuários):\n";
    $columns = $pdo->query('DESCRIBE empresa_usuarios')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }

    echo "\nRegistros na tabela 'empresas':\n";
    $empresas = $pdo->query('SELECT id, razao_social, nome_fantasia FROM empresas LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);
    foreach ($empresas as $empresa) {
        echo "ID: {$empresa['id']}, Razão Social: {$empresa['razao_social']}, Nome Fantasia: {$empresa['nome_fantasia']}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
