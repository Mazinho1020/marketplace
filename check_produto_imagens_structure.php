<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== ESTRUTURA DA TABELA produto_imagens ===\n";
    $stmt = $pdo->query('DESCRIBE produto_imagens');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf(
            "%-20s | %-15s | %-10s | %-10s\n",
            $row['Field'],
            $row['Type'],
            $row['Null'],
            $row['Key']
        );
    }

    echo "\n=== RELACIONAMENTO COM PRODUTOS ===\n";
    $stmt = $pdo->query('SHOW CREATE TABLE produto_imagens');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $result['Create Table'];

    echo "\n\n=== TESTE DE MÚLTIPLAS IMAGENS ===\n";
    echo "Verificando se permite múltiplas imagens por produto...\n";

    // Verificar se existe constraint UNIQUE que impediria múltiplas imagens
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME, COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'meufinanceiro' 
        AND TABLE_NAME = 'produto_imagens'
        AND CONSTRAINT_NAME != 'PRIMARY'
    ");

    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($constraints)) {
        echo "✅ SIM! Permite múltiplas imagens por produto (sem constraints UNIQUE)\n";
    } else {
        echo "❌ Pode ter limitações:\n";
        foreach ($constraints as $constraint) {
            echo "  - {$constraint['CONSTRAINT_NAME']}: {$constraint['COLUMN_NAME']}\n";
        }
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
