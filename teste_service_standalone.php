<?php

require_once __DIR__ . '/vendor/autoload.php';

echo "=== TESTE BÁSICO SISTEMA LARAVEL NATIVO ===\n\n";

try {
    // Primeiro: testar o service isoladamente
    echo "1. Testando DatabaseEnvironmentService (standalone):\n";

    $service = \App\Services\Database\DatabaseEnvironmentService::getInstance();
    echo "   - Service criado: ✓\n";
    echo "   - Ambiente detectado: " . $service->getCurrentEnvironment() . "\n";

    $config = $service->getConfig();
    if ($config) {
        echo "   - Configuração carregada: ✓\n";
        echo "   - Banco: {$config['database']} @ {$config['host']}\n";
        echo "   - Nome: {$config['connection_name']}\n";
    } else {
        echo "   - Configuração: Usando padrão (.env)\n";
    }

    // Testar informações de debug
    echo "\n2. Debug Info:\n";
    $debug = $service->getDebugInfo();
    echo "   - APP_ENV: " . $debug['detection_info']['app_env'] . "\n";
    echo "   - É local: " . ($debug['detection_info']['is_local'] ? 'Sim' : 'Não') . "\n";
    echo "   - CWD: " . $debug['detection_info']['cwd'] . "\n";

    // Testar conexão (sem Laravel framework carregado)
    echo "\n3. Teste de Conexão (PDO direto):\n";

    try {
        $connectionTest = $service->testConnection();
        echo "   - Conexão: " . ($connectionTest ? '✓ OK' : '✗ FALHA') . "\n";

        if ($connectionTest && $config) {
            // Testar query direta
            $pdo = new PDO(
                "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
                $config['username'],
                $config['password']
            );

            $result = $pdo->query('SELECT DATABASE() as db')->fetch();
            echo "   - Banco conectado: " . $result['db'] . "\n";

            // Verificar tabelas de config
            $tables = $pdo->query("SHOW TABLES LIKE 'config_%'")->fetchAll();
            echo "   - Tabelas config encontradas: " . count($tables) . "\n";
        }
    } catch (Exception $e) {
        echo "   - Erro na conexão: " . $e->getMessage() . "\n";
    }

    echo "\n=== TESTE BÁSICO CONCLUÍDO ===\n";
    echo "Service standalone funcionando!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}
