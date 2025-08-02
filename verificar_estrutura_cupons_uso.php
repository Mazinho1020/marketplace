<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
$result = $pdo->query('DESCRIBE fidelidade_cupons_uso');
echo "Estrutura da tabela fidelidade_cupons_uso:\n";
while ($row = $result->fetch()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
