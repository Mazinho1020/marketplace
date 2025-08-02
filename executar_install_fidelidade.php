<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=meufinanceiro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conectado ao banco de dados...\n";

    // Ler o arquivo SQL
    $sql = file_get_contents('install_fidelidade.sql');

    // Dividir as queries por ponto e vírgula
    $queries = explode(';', $sql);

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                $pdo->exec($query);
                echo "✅ Query executada com sucesso\n";
            } catch (Exception $e) {
                echo "❌ Erro na query: " . $e->getMessage() . "\n";
                echo "Query: " . substr($query, 0, 100) . "...\n\n";
            }
        }
    }

    echo "\n=== VERIFICANDO TABELAS CRIADAS ===\n";

    $tabelas = [
        'fidelidade_programas',
        'fidelidade_cartoes',
        'fidelidade_carteiras',
        'fidelidade_cashback_regras',
        'fidelidade_cashback_transacoes',
        'fidelidade_creditos',
        'fidelidade_conquistas',
        'fidelidade_cliente_conquistas',
        'fidelidade_cupons'
    ];

    foreach ($tabelas as $tabela) {
        $result = $pdo->query("SHOW TABLES LIKE '$tabela'");
        if ($result->rowCount() > 0) {
            echo "✅ $tabela - criada\n";
        } else {
            echo "❌ $tabela - não encontrada\n";
        }
    }

    echo "\nInstalação do sistema de fidelidade concluída!\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
}
