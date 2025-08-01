<?php
echo "Teste simples\n";
echo "PHP: " . phpversion() . "\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    echo "Conectado ao banco\n";

    $result = $pdo->query("SELECT COUNT(*) as total FROM config_environments")->fetch();
    echo "Ambientes: " . $result['total'] . "\n";

    $result = $pdo->query("SELECT COUNT(*) as total FROM config_db_connections WHERE deleted_at IS NULL")->fetch();
    echo "Conexões: " . $result['total'] . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
echo "Fim do teste\n";
