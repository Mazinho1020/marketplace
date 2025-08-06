<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
echo "Estrutura da tabela empresa_usuarios:\n";
$result = $pdo->query('DESCRIBE empresa_usuarios');
while ($row = $result->fetch()) {
    echo "{$row[0]} - {$row[1]}\n";
}
