<?php
// Script simples para restaurar banco
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'meufinanceiro';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conectado ao banco!\n";

    // Desabilitar foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    $sql = file_get_contents('backup_restore.sql');
    $pdo->exec($sql);

    // Reabilitar foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "Restauração concluída!\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
