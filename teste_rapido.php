<?php

/**
 * TESTE ROBUSTO DE CONFIGURAÇÃO DE BANCO
 * Execute: php teste_rapido.php
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== TESTE ROBUSTO DE CONFIGURAÇÃO ===\n";

try {
    // Usar o serviço robusto
    $service = \App\Services\DatabaseEnvironmentService::getInstance();

    // Informações de detecção
    $debugInfo = $service->getDebugInfo();

    echo "Ambiente detectado: " . $debugInfo['environment'] . "\n";
    echo "Teste de conexão: " . ($debugInfo['connection_test'] ? '✅ SUCESSO' : '❌ FALHA') . "\n";

    echo "\nConfiguração aplicada:\n";
    echo "  Host: " . $debugInfo['config']['host'] . "\n";
    echo "  Database: " . $debugInfo['config']['database'] . "\n";
    echo "  Username: " . $debugInfo['config']['username'] . "\n";
    echo "  Password: " . $debugInfo['config']['password'] . "\n";

    echo "\nInformações de detecção:\n";
    foreach ($debugInfo['detection_info'] as $key => $value) {
        echo "  {$key}: {$value}\n";
    }

    // Teste direto de conexão
    $config = $service->getConfig();
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
        $config['username'],
        $config['password']
    );

    $actualDb = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "\n✅ Conexão direta bem-sucedida!\n";
    echo "✅ Banco real conectado: {$actualDb}\n";

    // Verificar se é o banco correto
    if ($service->getEnvironment() === 'desenvolvimento') {
        if ($actualDb === 'meufinanceiro') {
            echo "🎉 PERFEITO: Desenvolvimento usando banco LOCAL!\n";
        } else {
            echo "❌ ERRO: Desenvolvimento deveria usar 'meufinanceiro', mas está usando '{$actualDb}'!\n";
        }
    } else {
        if (str_contains($actualDb, 'finanp06_')) {
            echo "🎉 PERFEITO: Produção usando banco ONLINE!\n";
        } else {
            echo "⚠️  ATENÇÃO: Produção usando banco '{$actualDb}'!\n";
        }
    }

    // Testar se as tabelas de configuração existem
    $stmt = $pdo->query("SHOW TABLES LIKE 'config_%'");
    $configTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "\n✅ Tabelas de configuração encontradas: " . count($configTables) . "\n";

    if (in_array('config_environments', $configTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM config_environments");
        $total = $stmt->fetchColumn();
        echo "✅ Total de ambientes configurados: {$total}\n";
    }

    if (in_array('config_db_connections', $configTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM config_db_connections");
        $total = $stmt->fetchColumn();
        echo "✅ Total de conexões configuradas: {$total}\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
