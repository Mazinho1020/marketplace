<?php
// Script de Linha de Comando para Alternar Ambientes
// Uso: php trocar_ambiente.php [dev|homolog|prod]

// Verificar parâmetro
if ($argc < 2 || !in_array($argv[1], ['dev', 'homolog', 'prod', 'status'])) {
    echo "🔧 GERENCIADOR DE AMBIENTE DE BANCO DE DADOS\n";
    echo "═══════════════════════════════════════════════\n\n";
    echo "Uso: php trocar_ambiente.php [comando]\n\n";
    echo "Comandos disponíveis:\n";
    echo "  dev      - Alterar para DESENVOLVIMENTO\n";
    echo "  homolog  - Alterar para HOMOLOGAÇÃO\n";
    echo "  prod     - Alterar para PRODUÇÃO\n";
    echo "  status   - Mostrar status atual\n\n";
    echo "Exemplos:\n";
    echo "  php trocar_ambiente.php dev\n";
    echo "  php trocar_ambiente.php prod\n";
    echo "  php trocar_ambiente.php status\n";
    exit(1);
}

$comando = $argv[1];

// Mapear parâmetro para ambiente
$ambienteMap = [
    'dev' => 'desenvolvimento',
    'homolog' => 'homologacao',
    'prod' => 'producao'
];

try {
    if ($comando === 'status') {
        // Mostrar status atual
        echo "📊 STATUS ATUAL DO SISTEMA\n";
        echo "═══════════════════════════\n\n";

        $configPath = __DIR__ . '/config/database_simples.php';
        if (!file_exists($configPath)) {
            echo "❌ Arquivo de configuração não encontrado!\n";
            exit(1);
        }

        $config = require $configPath;

        // Carregar o gerenciador de conexões
        require_once __DIR__ . '/app/Services/Database/ConnectionManagerSimples.php';
        $manager = \App\Services\Database\ConnectionManagerSimples::getInstance();
        $info = $manager->getInfoSistema();

        echo "🎯 Ambiente Atual: " . strtoupper($info['ambiente_atual']) . "\n";
        echo "🔧 Detecção Automática: " . ($info['deteccao_auto'] ? 'ATIVADA' : 'DESATIVADA') . "\n";
        echo "🖥️ Hostname: {$info['hostname']}\n";
        echo "📁 Diretório: {$info['working_dir']}\n\n";

        echo "🔗 Conexão Atual:\n";
        $conn = $info['conexao_atual'];
        echo "   Host: {$conn['host']}:{$conn['porta']}\n";
        echo "   Banco: {$conn['banco']}\n";
        echo "   Usuário: {$conn['usuario']}\n\n";

        // Testar todas as conexões
        echo "🧪 TESTE DE CONEXÕES:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━\n";

        foreach (['desenvolvimento', 'homologacao', 'producao'] as $amb) {
            $teste = $manager->testarConexao($amb);
            $emoji = $teste['sucesso'] ? '✅' : '❌';
            $status = $teste['sucesso'] ? 'OK' : 'ERRO';

            echo "{$emoji} " . strtoupper($amb) . ": {$status}";
            if ($teste['sucesso']) {
                echo " ({$teste['host']}/{$teste['banco']})";
            } else {
                echo " - {$teste['erro']}";
            }
            echo "\n";
        }

        // Histórico de mudanças
        if (!empty($info['historico_mudancas'])) {
            echo "\n📝 HISTÓRICO DE MUDANÇAS (últimas 5):\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

            $historico = array_slice($info['historico_mudancas'], -5);
            foreach ($historico as $mudanca) {
                echo "   {$mudanca['data_mudanca']}: ";
                echo strtoupper($mudanca['ambiente_anterior']) . " → " . strtoupper($mudanca['ambiente_novo']);
                echo " ({$mudanca['ip']})\n";
            }
        }
    } else {
        // Alterar ambiente
        $novoAmbiente = $ambienteMap[$comando];

        echo "🔄 ALTERANDO AMBIENTE PARA: " . strtoupper($novoAmbiente) . "\n";
        echo "═══════════════════════════════════════════════════════\n\n";

        // Carregar o gerenciador de conexões
        require_once __DIR__ . '/app/Services/Database/ConnectionManagerSimples.php';
        $manager = \App\Services\Database\ConnectionManagerSimples::getInstance();
        $ambienteAtual = $manager->getAmbiente();

        if ($ambienteAtual === $novoAmbiente) {
            echo "ℹ️ O ambiente já está configurado como " . strtoupper($novoAmbiente) . "\n";
            echo "✅ Nenhuma alteração necessária.\n";
        } else {
            echo "📋 Ambiente atual: " . strtoupper($ambienteAtual) . "\n";
            echo "🎯 Novo ambiente: " . strtoupper($novoAmbiente) . "\n\n";

            // Testar conexão antes de alterar
            echo "🧪 Testando conexão com o novo ambiente...\n";
            $teste = $manager->testarConexao($novoAmbiente);

            if (!$teste['sucesso']) {
                echo "❌ ERRO: Não foi possível conectar ao ambiente {$novoAmbiente}:\n";
                echo "   {$teste['erro']}\n\n";
                echo "⚠️ Alteração cancelada por segurança.\n";
                echo "   Verifique as configurações em config/database_simples.php\n";
                exit(1);
            }

            echo "✅ Conexão testada com sucesso!\n";
            echo "   Host: {$teste['host']}\n";
            echo "   Banco: {$teste['banco']}\n\n";

            // Proceder com a alteração
            echo "🔧 Aplicando alteração...\n";

            if ($manager->alternarAmbiente($novoAmbiente)) {
                echo "✅ AMBIENTE ALTERADO COM SUCESSO!\n\n";

                $conexaoNova = $manager->getConexaoAtual();
                echo "📊 Nova configuração ativa:\n";
                echo "   Ambiente: " . strtoupper($novoAmbiente) . "\n";
                echo "   Host: {$conexaoNova['host']}:{$conexaoNova['porta']}\n";
                echo "   Banco: {$conexaoNova['banco']}\n";
                echo "   Usuário: {$conexaoNova['usuario']}\n\n";

                echo "⚠️ IMPORTANTE:\n";
                echo "   • Reinicie a aplicação web para aplicar as mudanças\n";
                echo "   • Limpe qualquer cache da aplicação\n";
                echo "   • Execute 'php trocar_ambiente.php status' para verificar\n";
            } else {
                echo "❌ Erro ao alterar ambiente.\n";
                exit(1);
            }
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🏁 Operação concluída: " . date('Y-m-d H:i:s') . "\n";
