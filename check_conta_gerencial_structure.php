<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', 'root');
    $stmt = $pdo->query('DESCRIBE categorias_conta');

    echo "Estrutura da tabela categorias_conta:\n";
    echo "====================================\n";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
