<?php
echo "=== Removendo Tabela Desnecessária ===\n";

try {
    // Conectar ao banco
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'produto_fornecedores'");
    $exists = $stmt->fetch();

    if ($exists) {
        // Remover a tabela
        $pdo->exec('DROP TABLE produto_fornecedores');
        echo "✅ Tabela 'produto_fornecedores' removida com sucesso!\n";

        // Verificar se foi removida
        $stmt = $pdo->query("SHOW TABLES LIKE 'produto_fornecedores'");
        $stillExists = $stmt->fetch();

        if (!$stillExists) {
            echo "✅ Confirmado: Tabela foi removida do banco de dados.\n";
        }
    } else {
        echo "ℹ️ Tabela 'produto_fornecedores' não existe.\n";
    }

    // Listar tabelas restantes
    echo "\n=== Tabelas de Produtos Restantes ===\n";
    $stmt = $pdo->query("SHOW TABLES LIKE '%produto%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        echo "- $table\n";
    }

    echo "\nTotal: " . count($tables) . " tabelas\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
