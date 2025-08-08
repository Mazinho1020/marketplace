<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
$stmt = $pdo->query('DESCRIBE pessoas');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    if ($col['Field'] === 'id') {
        echo 'Tipo da coluna pessoas.id: ' . $col['Type'] . "\n";
        break;
    }
}
