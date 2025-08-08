<?php
echo "=== Verificando Tabelas de Produtos ===\n";

try {
    // Conectar ao banco
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar tabelas relacionadas a produtos
    $stmt = $pdo->query("SHOW TABLES LIKE '%produto%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "\nTabelas encontradas:\n";
    if (empty($tables)) {
        echo "- Nenhuma tabela de produto encontrada\n";
    } else {
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    }

    // Verificar se a tabela produto_imagems existe especificamente
    $stmt = $pdo->query("SHOW TABLES LIKE 'produto_imagems'");
    $exists = $stmt->fetch();

    echo "\nTabela 'produto_imagems':\n";
    echo $exists ? "✅ Existe\n" : "❌ Não existe\n";

    // Verificar estrutura da tabela produtos se existir
    $stmt = $pdo->query("SHOW TABLES LIKE 'produtos'");
    $produtosExists = $stmt->fetch();

    if ($produtosExists) {
        echo "\n=== Estrutura da tabela 'produtos' ===\n";
        $stmt = $pdo->query("DESCRIBE produtos");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']} ({$row['Type']}) {$row['Null']} {$row['Key']}\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
