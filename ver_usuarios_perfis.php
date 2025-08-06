<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', 'root');
echo "Papéis/Perfis existentes:\n";
$result = $pdo->query('SELECT * FROM empresa_papeis LIMIT 10');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Nome: {$row['nome']}\n";
}

echo "\nUsuários existentes:\n";
$result = $pdo->query('SELECT id, nome, email, perfil_id, status FROM empresa_usuarios LIMIT 5');
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: {$row['id']}, Nome: {$row['nome']}, Email: {$row['email']}, Perfil ID: {$row['perfil_id']}, Status: {$row['status']}\n";
}
