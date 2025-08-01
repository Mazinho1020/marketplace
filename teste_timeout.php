<?php
ini_set('max_execution_time', 5);
echo "Teste com timeout\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro;charset=utf8', 'root', '', [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Conectado!\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
echo "Fim\n";
