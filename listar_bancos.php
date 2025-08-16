<?php
$pdo = new PDO('mysql:host=localhost', 'root', 'root');
$dbs = $pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
echo "Bancos disponíveis:\n";
foreach($dbs as $db) {
    if(!in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
        echo "- $db\n";
    }
}
?>
