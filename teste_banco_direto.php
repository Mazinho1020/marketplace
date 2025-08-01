<?php

echo "🔍 TESTE DIRETO AO BANCO (PDO) 🔍\n";
echo "══════════════════════════════════\n\n";

try {
    // Conectar direto com PDO usando .env
    $host = '127.0.0.1';
    $port = 3306;
    $database = 'meufinanceiro';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conectado ao banco: {$database}\n\n";

    // Verificar ambientes
    echo "📋 AMBIENTES:\n";
    $stmt = $pdo->query("SELECT id, codigo, nome, is_producao, ativo FROM config_environments ORDER BY id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $prod = $row['is_producao'] ? '🏭 Prod' : '💻 Dev';
        $ativo = $row['ativo'] ? '✅' : '❌';
        echo "  {$row['id']}: {$row['nome']} ({$row['codigo']}) {$prod} {$ativo}\n";
    }

    // Verificar conexões
    echo "\n📋 CONEXÕES:\n";
    $stmt = $pdo->query("SELECT id, ambiente_id, nome, host, banco, padrao FROM config_db_connections WHERE deleted_at IS NULL ORDER BY ambiente_id, id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $padrao = $row['padrao'] ? '⭐ PADRÃO' : '';
        echo "  {$row['id']}: {$row['nome']} @ {$row['host']}/{$row['banco']} (Env: {$row['ambiente_id']}) {$padrao}\n";
    }

    echo "\n" . str_repeat("═", 50) . "\n";
    echo "💡 AGORA EXECUTE OS COMANDOS SQL E TESTE NOVAMENTE!\n\n";

    echo "🔄 COMANDOS SQL PARA EXECUTAR:\n";
    echo "UPDATE config_db_connections SET padrao = 1 WHERE ambiente_id = 2 AND nome = 'Banco Local';\n";
    echo "UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = 2 AND nome != 'Banco Local';\n";
    echo "UPDATE config_db_connections SET padrao = 1 WHERE ambiente_id = 1 AND nome = 'Banco Produção';\n";
    echo "UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = 1 AND nome != 'Banco Produção';\n\n";

    echo "Execute: php teste_banco_direto.php (para ver mudanças)\n";
} catch (PDOException $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
