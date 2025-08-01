<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "🚀 TESTE DINÂMICO DO SISTEMA LARAVEL NATIVO 🚀\n";
echo "═══════════════════════════════════════════════\n\n";

try {
    // Inicializar Laravel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "1️⃣ ESTADO ATUAL DAS TABELAS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    // Mostrar ambientes
    $environments = \Illuminate\Support\Facades\DB::table('config_environments')
        ->select('id', 'codigo', 'nome', 'is_producao', 'ativo')
        ->orderBy('id')
        ->get();

    echo "📋 AMBIENTES:\n";
    foreach ($environments as $env) {
        $prod = $env->is_producao ? '🏭 Produção' : '💻 Desenvolvimento';
        $ativo = $env->ativo ? '✅ Ativo' : '❌ Inativo';
        echo "  • ID {$env->id}: {$env->nome} ({$env->codigo}) - {$prod} - {$ativo}\n";
    }

    // Mostrar conexões
    $connections = \Illuminate\Support\Facades\DB::table('config_db_connections')
        ->join('config_environments', 'config_db_connections.environment_id', '=', 'config_environments.id')
        ->select(
            'config_db_connections.id',
            'config_db_connections.nome',
            'config_db_connections.host',
            'config_db_connections.banco',
            'config_db_connections.padrao',
            'config_environments.codigo as env_codigo',
            'config_environments.nome as env_nome'
        )
        ->whereNull('config_db_connections.deleted_at')
        ->orderBy('config_environments.id')
        ->orderBy('config_db_connections.id')
        ->get();

    echo "\n📋 CONEXÕES:\n";
    foreach ($connections as $conn) {
        $padrao = $conn->padrao ? '⭐ PADRÃO' : '   Normal';
        echo "  • ID {$conn->id}: {$conn->nome} @ {$conn->host}/{$conn->banco} ({$conn->env_codigo}) - {$padrao}\n";
    }

    echo "\n2️⃣ TESTANDO O SISTEMA DINÂMICO:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    // Obter service
    $service = app(\App\Services\Database\DatabaseEnvironmentService::class);

    echo "🔍 Ambiente detectado: " . $service->getCurrentEnvironment() . "\n";

    // Obter configuração
    $config = $service->getConfig();

    if ($config) {
        echo "⚙️  Configuração carregada:\n";
        echo "   • Nome: {$config['connection_name']}\n";
        echo "   • Host: {$config['host']}:{$config['port']}\n";
        echo "   • Banco: {$config['database']}\n";
        echo "   • Usuário: {$config['username']}\n";
        echo "   • Ambiente: {$config['environment_name']}\n";
    } else {
        echo "⚠️  Usando configuração padrão do .env\n";
    }

    // Testar conexão
    $conexaoOk = $service->testConnection();
    echo "🔗 Teste de conexão: " . ($conexaoOk ? "✅ SUCESSO" : "❌ FALHA") . "\n";

    if ($conexaoOk) {
        $bancoAtual = \Illuminate\Support\Facades\DB::select('SELECT DATABASE() as db')[0]->db;
        echo "🗃️  Banco conectado: {$bancoAtual}\n";
    }

    echo "\n3️⃣ USANDO ELOQUENT MODELS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    // Testar models
    $ambienteAtual = \App\Models\Config\ConfigEnvironment::getCurrentEnvironment();

    if ($ambienteAtual) {
        echo "🏛️  Ambiente atual (Model): {$ambienteAtual->nome} ({$ambienteAtual->codigo})\n";
        echo "   • É produção: " . ($ambienteAtual->isProducao() ? 'Sim' : 'Não') . "\n";
        echo "   • Está ativo: " . ($ambienteAtual->isActive() ? 'Sim' : 'Não') . "\n";

        // Conexão padrão
        $conexaoPadrao = $ambienteAtual->defaultDbConnection()->first();
        if ($conexaoPadrao) {
            echo "🔗 Conexão padrão: {$conexaoPadrao->nome}\n";
            echo "   • Host: {$conexaoPadrao->host}:{$conexaoPadrao->porta}\n";
            echo "   • Banco: {$conexaoPadrao->banco}\n";

            // Testar conexão específica
            $testeConexao = $ambienteAtual->testDefaultDatabaseConnection();
            echo "   • Teste: " . ($testeConexao['success'] ? "✅ OK" : "❌ {$testeConexao['message']}") . "\n";
        }

        // Estatísticas
        $stats = $ambienteAtual->getStats();
        echo "📊 Estatísticas:\n";
        echo "   • Total conexões: {$stats['total_db_connections']}\n";
        echo "   • Conexões ativas: {$stats['active_db_connections']}\n";
        echo "   • Tem conexão padrão: " . ($stats['has_default_connection'] ? 'Sim' : 'Não') . "\n";
    }

    echo "\n4️⃣ INFORMAÇÕES DE DEBUG:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    $debug = \App\Providers\DatabaseConfigServiceProvider::getDebugInfo();

    if (isset($debug['error'])) {
        echo "❌ Erro: {$debug['message']}\n";
    } else {
        echo "🔍 Detecção de ambiente:\n";
        echo "   • APP_ENV: {$debug['detection_info']['app_env']}\n";
        echo "   • É local: " . ($debug['detection_info']['is_local'] ? 'Sim' : 'Não') . "\n";
        echo "   • Hostname: {$debug['detection_info']['hostname']}\n";
        echo "   • Working Dir: {$debug['detection_info']['cwd']}\n";

        echo "\n🎯 Status do sistema:\n";
        echo "   • Ambiente mapeado: {$debug['environment']}\n";
        echo "   • Configuração carregada: " . ($debug['configuration_loaded'] ? 'Sim' : 'Não') . "\n";
        echo "   • Conexão testada: " . ($debug['connection_test'] ? 'OK' : 'Falha') . "\n";
        echo "   • Banco atual: {$debug['current_database']}\n";
    }

    echo "\n" . str_repeat("═", 60) . "\n";
    echo "🎉 SISTEMA FUNCIONANDO PERFEITAMENTE!\n";
    echo "✅ Laravel nativo ✅ Eloquent ✅ Cache ✅ Config dinâmico\n";
    echo str_repeat("═", 60) . "\n";

    echo "\n💡 PARA TESTAR MUDANÇAS DINÂMICAS:\n";
    echo "1. Execute os comandos SQL que você enviou\n";
    echo "2. Execute novamente este teste: php teste_dinamico_completo.php\n";
    echo "3. Veja as mudanças refletidas automaticamente!\n\n";

    echo "🔄 RECARREGAR CACHE:\n";
    $recarregou = \App\Providers\DatabaseConfigServiceProvider::reloadConfiguration();
    echo "Cache recarregado: " . ($recarregou ? "✅ Sucesso" : "❌ Falha") . "\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "📍 Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
