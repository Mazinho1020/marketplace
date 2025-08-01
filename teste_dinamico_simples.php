<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 TESTE DINÂMICO SIMPLIFICADO 🚀\n";
echo "═════════════════════════════════\n\n";

try {
    // 1. Estado das tabelas
    echo "📋 AMBIENTES:\n";
    $envs = \Illuminate\Support\Facades\DB::table('config_environments')->get();
    foreach ($envs as $env) {
        echo "  {$env->id}: {$env->nome} ({$env->codigo}) - " . ($env->ativo ? "Ativo" : "Inativo") . "\n";
    }

    echo "\n📋 CONEXÕES:\n";
    $conns = \Illuminate\Support\Facades\DB::table('config_db_connections')
        ->whereNull('deleted_at')
        ->get();
    foreach ($conns as $conn) {
        $padrao = isset($conn->padrao) && $conn->padrao ? " ⭐ PADRÃO" : "";
        echo "  {$conn->id}: {$conn->nome} @ {$conn->host}/{$conn->banco} (Env: {$conn->ambiente_id}){$padrao}\n";
    }

    // 2. Sistema atual
    echo "\n🔍 SISTEMA ATUAL:\n";
    $service = app(\App\Services\Database\DatabaseEnvironmentService::class);

    echo "Ambiente detectado: " . $service->getCurrentEnvironment() . "\n";

    $config = $service->getConfig();
    if ($config) {
        echo "Configuração: {$config['connection_name']}\n";
        echo "Banco: {$config['database']} @ {$config['host']}\n";
    } else {
        echo "Configuração: Padrão (.env)\n";
    }

    $conexao = $service->testConnection();
    echo "Conexão: " . ($conexao ? "✅ OK" : "❌ Falha") . "\n";

    if ($conexao) {
        $db = \Illuminate\Support\Facades\DB::select('SELECT DATABASE() as db')[0]->db;
        echo "Banco conectado: {$db}\n";
    }

    echo "\n" . str_repeat("═", 40) . "\n";
    echo "💡 AGORA EXECUTE SEUS COMANDOS SQL:\n\n";

    $sqlCommands = [
        "-- Definir Banco Local como padrão para desenvolvimento",
        "UPDATE config_db_connections SET padrao = 1 WHERE ambiente_id = (SELECT id FROM config_environments WHERE codigo = 'desenvolvimento' LIMIT 1) AND nome = 'Banco Local';",
        "",
        "-- Remover padrão de outras conexões do desenvolvimento",
        "UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = (SELECT id FROM config_environments WHERE codigo = 'desenvolvimento' LIMIT 1) AND nome != 'Banco Local';",
        "",
        "-- Definir Banco Produção como padrão para produção",
        "UPDATE config_db_connections SET padrao = 1 WHERE ambiente_id = (SELECT id FROM config_environments WHERE codigo = 'producao' LIMIT 1) AND nome = 'Banco Produção';",
        "",
        "-- Remover padrão de outras conexões da produção",
        "UPDATE config_db_connections SET padrao = 0 WHERE ambiente_id = (SELECT id FROM config_environments WHERE codigo = 'producao' LIMIT 1) AND nome != 'Banco Produção';"
    ];

    foreach ($sqlCommands as $cmd) {
        echo $cmd . "\n";
    }

    echo "\n" . str_repeat("═", 40) . "\n";
    echo "🔄 Depois execute novamente: php teste_dinamico_simples.php\n";
    echo "para ver as mudanças!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
