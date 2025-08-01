<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Services\Database\DatabaseEnvironmentService;
use App\Providers\DatabaseConfigServiceProvider;
use App\Models\Config\ConfigEnvironment;
use App\Models\Config\ConfigDbConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== TESTE COMPLETO DO SISTEMA LARAVEL NATIVO ===\n\n";

try {
    // 1. Testar Service
    echo "1. Testando DatabaseEnvironmentService:\n";
    $service = DatabaseEnvironmentService::getInstance();

    echo "   - Ambiente detectado: " . $service->getCurrentEnvironment() . "\n";
    echo "   - Teste de conexão: " . ($service->testConnection() ? '✓ OK' : '✗ FALHA') . "\n";

    $config = $service->getConfig();
    if ($config) {
        echo "   - Banco configurado: {$config['database']} ({$config['host']})\n";
        echo "   - Nome da configuração: {$config['connection_name']}\n";
    }

    // 2. Testar informações de debug
    echo "\n2. Informações de Debug:\n";
    $debugInfo = $service->getDebugInfo();

    echo "   - APP_ENV: " . $debugInfo['detection_info']['app_env'] . "\n";
    echo "   - Is Local: " . ($debugInfo['detection_info']['is_local'] ? 'Sim' : 'Não') . "\n";
    echo "   - Hostname: " . $debugInfo['detection_info']['hostname'] . "\n";
    echo "   - Request Host: " . ($debugInfo['detection_info']['request_host'] ?? 'N/A') . "\n";
    echo "   - Working Directory: " . $debugInfo['detection_info']['cwd'] . "\n";

    // 3. Testar Models Eloquent
    echo "\n3. Testando Models Eloquent:\n";

    try {
        // Testar ConfigEnvironment
        $currentEnv = ConfigEnvironment::getCurrentEnvironment();

        if ($currentEnv) {
            echo "   - Ambiente atual (Model): {$currentEnv->nome} ({$currentEnv->codigo})\n";
            echo "   - É produção: " . ($currentEnv->isProducao() ? 'Sim' : 'Não') . "\n";
            echo "   - Está ativo: " . ($currentEnv->isActive() ? 'Sim' : 'Não') . "\n";
            echo "   - Status sync: {$currentEnv->sync_status_text}\n";

            // Testar relacionamentos
            $stats = $currentEnv->getStats();
            echo "   - Total de conexões DB: {$stats['total_db_connections']}\n";
            echo "   - Conexões ativas: {$stats['active_db_connections']}\n";
            echo "   - Tem conexão padrão: " . ($stats['has_default_connection'] ? 'Sim' : 'Não') . "\n";

            // Testar conexão padrão
            $defaultConnection = $currentEnv->defaultDbConnection()->first();
            if ($defaultConnection) {
                echo "   - Conexão padrão: {$defaultConnection->nome}\n";
                echo "   - Host: {$defaultConnection->host}:{$defaultConnection->porta}\n";
                echo "   - Banco: {$defaultConnection->banco}\n";

                // Testar conexão
                $connectionTest = $currentEnv->testDefaultDatabaseConnection();
                echo "   - Teste conexão padrão: " . ($connectionTest['success'] ? '✓ OK' : '✗ FALHA') . "\n";
                if (!$connectionTest['success']) {
                    echo "     Erro: {$connectionTest['message']}\n";
                }
            }
        } else {
            echo "   - ⚠️ Nenhum ambiente encontrado nos models\n";
        }
    } catch (Exception $e) {
        echo "   - ❌ Erro ao testar models: " . $e->getMessage() . "\n";
    }

    // 4. Testar Provider
    echo "\n4. Testando DatabaseConfigServiceProvider:\n";

    try {
        $providerDebug = DatabaseConfigServiceProvider::getDebugInfo();

        if (isset($providerDebug['error'])) {
            echo "   - ❌ Erro no provider: {$providerDebug['message']}\n";
        } else {
            echo "   - ✓ Provider funcionando\n";
            echo "   - Ambiente: {$providerDebug['environment']}\n";
            echo "   - Configuração carregada: " . ($providerDebug['configuration_loaded'] ? 'Sim' : 'Não') . "\n";
            echo "   - Banco atual: {$providerDebug['current_database']}\n";
        }
    } catch (Exception $e) {
        echo "   - ❌ Erro ao testar provider: " . $e->getMessage() . "\n";
    }

    // 5. Testar Cache
    echo "\n5. Testando Sistema de Cache:\n";

    try {
        // Recarregar configuração (testando cache)
        echo "   - Recarregando configuração...\n";
        $reloadSuccess = DatabaseConfigServiceProvider::reloadConfiguration();
        echo "   - Recarga: " . ($reloadSuccess ? '✓ Sucesso' : '✗ Falha') . "\n";

        // Verificar se conexão ainda funciona
        $newConnectionTest = $service->testConnection();
        echo "   - Conexão após recarga: " . ($newConnectionTest ? '✓ OK' : '✗ FALHA') . "\n";
    } catch (Exception $e) {
        echo "   - ❌ Erro ao testar cache: " . $e->getMessage() . "\n";
    }

    // 6. Testar queries diretas
    echo "\n6. Testando Queries no Banco:\n";

    try {
        $currentDb = DB::connection()->getDatabaseName();
        echo "   - Banco conectado: {$currentDb}\n";

        // Testar uma query simples
        $result = DB::select('SELECT DATABASE() as current_db');
        echo "   - Query SELECT DATABASE(): {$result[0]->current_db}\n";

        // Contar registros nas tabelas de config
        $envCount = DB::table('config_environments')->count();
        $connCount = DB::table('config_db_connections')->count();

        echo "   - Registros config_environments: {$envCount}\n";
        echo "   - Registros config_db_connections: {$connCount}\n";

        // Verificar ambiente ativo
        $activeEnv = DB::table('config_environments')
            ->where('ativo', 1)
            ->where('codigo', $service->getCurrentEnvironment())
            ->first();

        if ($activeEnv) {
            echo "   - Ambiente ativo encontrado: {$activeEnv->nome}\n";
        } else {
            echo "   - ⚠️ Ambiente ativo não encontrado na tabela\n";
        }
    } catch (Exception $e) {
        echo "   - ❌ Erro ao testar queries: " . $e->getMessage() . "\n";
    }

    echo "\n=== TESTE CONCLUÍDO ===\n";
    echo "Sistema usando funcionalidades nativas do Laravel funcionando!\n";
} catch (Exception $e) {
    echo "❌ ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
