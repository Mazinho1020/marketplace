<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "Tabelas no banco meufinanceiro:\n";
foreach ($tables as $table) {
    echo "- $table\n";
}
