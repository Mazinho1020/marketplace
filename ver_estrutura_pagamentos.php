<?php
$pdo = new PDO('mysql:host=localhost;dbname=meufinanceiro', 'root', 'root');
$result = $pdo->query('SHOW CREATE TABLE pagamentos');
$createTable = $result->fetch(PDO::FETCH_ASSOC)['Create Table'];
echo $createTable;
?>
