<?php

echo "🧪 TESTE SISTEMA SIMPLIFICADO 🧪\n";
echo "═══════════════════════════════════\n\n";

try {
    echo "1. Verificando arquivo de configuração...\n";
    $configPath = __DIR__ . '/config/database_simples.php';

    if (!file_exists($configPath)) {
        echo "❌ Arquivo de configuração não encontrado: {$configPath}\n";
        exit(1);
    }

    echo "✅ Arquivo encontrado: {$configPath}\n";

    echo "\n2. Carregando configuração...\n";
    $config = require $configPath;

    echo "✅ Configuração carregada\n";
    echo "   Ambiente: {$config['ambiente']}\n";
    echo "   Detecção Auto: " . ($config['deteccao_auto'] ? 'SIM' : 'NÃO') . "\n";
    echo "   Ambientes disponíveis: " . implode(', ', array_keys($config['conexoes'])) . "\n";

    echo "\n3. Testando classe ConnectionManagerSimples...\n";

    if (!file_exists('app/Services/Database/ConnectionManagerSimples.php')) {
        echo "❌ Classe não encontrada\n";
        exit(1);
    }

    require_once 'app/Services/Database/ConnectionManagerSimples.php';
    echo "✅ Classe carregada\n";

    echo "\n4. Instanciando gerenciador...\n";
    $manager = \App\Services\Database\ConnectionManagerSimples::getInstance();
    echo "✅ Gerenciador criado\n";

    echo "\n5. Obtendo informações do sistema...\n";
    $info = $manager->getInfoSistema();

    echo "✅ Informações obtidas:\n";
    echo "   Ambiente atual: {$info['ambiente_atual']}\n";
    echo "   Hostname: {$info['hostname']}\n";
    echo "   Diretório: {$info['working_dir']}\n";
    echo "   Detecção auto: " . ($info['deteccao_auto'] ? 'SIM' : 'NÃO') . "\n";

    echo "\n6. Testando conexão atual...\n";
    $pdo = $manager->getPDO();
    echo "✅ PDO obtido\n";

    $stmt = $pdo->query("SELECT 1 as teste, DATABASE() as banco_atual");
    $resultado = $stmt->fetch();

    echo "✅ Consulta executada com sucesso\n";
    echo "   Teste: {$resultado['teste']}\n";
    echo "   Banco atual: {$resultado['banco_atual']}\n";

    echo "\n7. Testando todas as conexões...\n";
    foreach (['desenvolvimento', 'homologacao', 'producao'] as $ambiente) {
        $teste = $manager->testarConexao($ambiente);
        $status = $teste['sucesso'] ? '✅' : '❌';

        echo "   {$status} {$ambiente}: ";
        if ($teste['sucesso']) {
            echo "{$teste['host']}/{$teste['banco']}\n";
        } else {
            echo "ERRO - {$teste['erro']}\n";
        }
    }

    echo "\n" . str_repeat("═", 50) . "\n";
    echo "🎉 SISTEMA SIMPLIFICADO FUNCIONANDO PERFEITAMENTE!\n";
    echo str_repeat("═", 50) . "\n\n";

    echo "📋 PARA USAR O SISTEMA:\n";
    echo "• Script CLI: php trocar_ambiente.php [dev|homolog|prod|status]\n";
    echo "• Interface Web: http://localhost/marketplace/public/gerenciar_ambiente.php\n";
    echo "• Configuração: config/database_simples.php\n\n";

    echo "🔧 EXEMPLOS DE USO:\n";
    echo "php trocar_ambiente.php dev      # Alterar para desenvolvimento\n";
    echo "php trocar_ambiente.php prod     # Alterar para produção\n";
    echo "php trocar_ambiente.php status   # Ver status atual\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Teste concluído: " . date('Y-m-d H:i:s') . "\n";
