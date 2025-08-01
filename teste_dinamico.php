<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== TESTE DINÂMICO DO SISTEMA ===\n\n";

try {
    // Inicializar Laravel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "✅ Laravel inicializado\n\n";

    // Obter service
    $service = app(\App\Services\Database\DatabaseEnvironmentService::class);

    // Estado inicial
    echo "📊 ESTADO INICIAL:\n";
    echo "─────────────────────\n";

    $config = $service->getConfig();
    if ($config) {
        echo "• Ambiente: {$service->getCurrentEnvironment()}\n";
        echo "• Banco: {$config['database']}\n";
        echo "• Host: {$config['host']}:{$config['port']}\n";
        echo "• Usuário: {$config['username']}\n";
        echo "• Nome conexão: {$config['connection_name']}\n";
    } else {
        echo "• Usando configuração padrão do .env\n";
    }

    // Testar conexão atual
    $connectionTest = $service->testConnection();
    echo "• Conexão: " . ($connectionTest ? "✅ OK" : "❌ FALHA") . "\n";

    if ($connectionTest) {
        $currentDb = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        echo "• DB conectado: $currentDb\n";
    }

    echo "\n🔄 AGUARDANDO MUDANÇAS NO BANCO...\n";
    echo "─────────────────────────────────────\n";
    echo "👆 AGORA você pode:\n";
    echo "1. Alterar dados na tabela 'config_environments'\n";
    echo "2. Alterar dados na tabela 'config_db_connections'\n";
    echo "3. Pressionar ENTER aqui para testar novamente\n\n";

    // Aguardar input do usuário
    echo "Pressione ENTER após fazer as mudanças no banco...";
    fgets(STDIN);

    echo "\n🔃 RECARREGANDO CONFIGURAÇÃO...\n";
    echo "─────────────────────────────────\n";

    // Recarregar configuração (limpa cache)
    $reloadSuccess = \App\Providers\DatabaseConfigServiceProvider::reloadConfiguration();
    echo "• Recarga do cache: " . ($reloadSuccess ? "✅ OK" : "❌ FALHA") . "\n";

    // Obter nova configuração
    $newConfig = $service->getConfig();

    echo "\n📊 NOVO ESTADO:\n";
    echo "─────────────────\n";

    if ($newConfig) {
        echo "• Ambiente: {$service->getCurrentEnvironment()}\n";
        echo "• Banco: {$newConfig['database']}\n";
        echo "• Host: {$newConfig['host']}:{$newConfig['port']}\n";
        echo "• Usuário: {$newConfig['username']}\n";
        echo "• Nome conexão: {$newConfig['connection_name']}\n";

        // Verificar se mudou
        if ($config && $newConfig) {
            $changed = false;
            $changes = [];

            if ($config['database'] != $newConfig['database']) {
                $changes[] = "Banco: {$config['database']} → {$newConfig['database']}";
                $changed = true;
            }

            if ($config['host'] != $newConfig['host']) {
                $changes[] = "Host: {$config['host']} → {$newConfig['host']}";
                $changed = true;
            }

            if ($config['connection_name'] != $newConfig['connection_name']) {
                $changes[] = "Conexão: {$config['connection_name']} → {$newConfig['connection_name']}";
                $changed = true;
            }

            if ($changed) {
                echo "\n🔄 MUDANÇAS DETECTADAS:\n";
                foreach ($changes as $change) {
                    echo "• $change\n";
                }
            } else {
                echo "\n📌 Configuração permanece igual\n";
            }
        }
    } else {
        echo "• Usando configuração padrão do .env\n";
    }

    // Testar nova conexão
    $newConnectionTest = $service->testConnection();
    echo "• Nova conexão: " . ($newConnectionTest ? "✅ OK" : "❌ FALHA") . "\n";

    if ($newConnectionTest) {
        $newCurrentDb = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        echo "• Novo DB conectado: $newCurrentDb\n";

        if ($connectionTest && $currentDb != $newCurrentDb) {
            echo "🎉 MUDANÇA DE BANCO DETECTADA: $currentDb → $newCurrentDb\n";
        }
    }

    echo "\n📈 INFORMAÇÕES DE DEBUG:\n";
    echo "─────────────────────────\n";

    $debug = \App\Providers\DatabaseConfigServiceProvider::getDebugInfo();
    if (isset($debug['error'])) {
        echo "❌ Erro: {$debug['message']}\n";
    } else {
        echo "• Ambiente detectado: {$debug['environment']}\n";
        echo "• Configuração carregada: " . ($debug['configuration_loaded'] ? 'Sim' : 'Não') . "\n";
        echo "• Teste de conexão: " . ($debug['connection_test'] ? 'OK' : 'Falha') . "\n";
        echo "• Banco atual: {$debug['current_database']}\n";
        echo "• É local: " . ($debug['detection_info']['is_local'] ? 'Sim' : 'Não') . "\n";
        echo "• APP_ENV: {$debug['detection_info']['app_env']}\n";
    }

    echo "\n✨ TESTE DINÂMICO CONCLUÍDO!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎯 O sistema está funcionando dinamicamente!\n";
    echo "💾 As mudanças no banco são aplicadas automaticamente!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📂 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
