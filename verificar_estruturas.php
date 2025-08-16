<?php
$pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', 'root');
$result = $pdo->query('DESCRIBE empresas');
echo "Estrutura da tabela empresas:\n";
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\nEstrutura da tabela usuarios:\n";
$result = $pdo->query('DESCRIBE usuarios');
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
