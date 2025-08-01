<?php
// Script Simplificado para Alternar Ambientes
// Uso: php ambiente.php [dev|homolog|prod|status]

// Verificar parâmetro
if ($argc < 2 || !in_array($argv[1], ['dev', 'homolog', 'prod', 'status'])) {
    echo "🔧 GERENCIADOR DE AMBIENTE DE BANCO DE DADOS\n";
    echo "═══════════════════════════════════════════════\n\n";
    echo "Uso: php ambiente.php [comando]\n\n";
    echo "Comandos disponíveis:\n";
    echo "  dev      - Alterar para DESENVOLVIMENTO\n";
    echo "  homolog  - Alterar para HOMOLOGAÇÃO\n";
    echo "  prod     - Alterar para PRODUÇÃO\n";
    echo "  status   - Mostrar status atual\n\n";
    echo "Exemplos:\n";
    echo "  php ambiente.php dev\n";
    echo "  php ambiente.php prod\n";
    echo "  php ambiente.php status\n";
    exit(1);
}

$comando = $argv[1];

// Mapear parâmetro para ambiente
$ambienteMap = [
    'dev' => 'desenvolvimento',
    'homolog' => 'homologacao',
    'prod' => 'producao'
];

function detectarAmbiente()
{
    $hostname = gethostname();
    $cwd = getcwd();

    $isLocal = (
        str_contains(strtolower($hostname), 'desktop') ||
        str_contains(strtolower($hostname), 'laptop') ||
        str_contains(strtolower($hostname), 'servidor') ||
        str_contains($cwd, 'xampp') ||
        str_contains($cwd, 'laragon') ||
        str_contains($cwd, 'wamp') ||
        ($_SERVER['SERVER_NAME'] ?? '') === 'localhost' ||
        ($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' ||
        ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1'
    );

    if ($isLocal) {
        return 'desenvolvimento';
    } else if (str_contains($_SERVER['HTTP_HOST'] ?? '', 'homolog')) {
        return 'homologacao';
    } else {
        return 'producao';
    }
}

function testarConexao($config, $ambiente)
{
    if (!isset($config['conexoes'][$ambiente])) {
        return ['sucesso' => false, 'erro' => "Configuração para '{$ambiente}' não encontrada"];
    }

    $conn = $config['conexoes'][$ambiente];

    try {
        $dsn = "{$conn['driver']}:host={$conn['host']};port={$conn['porta']};dbname={$conn['banco']};charset={$conn['charset']}";
        $testPdo = new PDO($dsn, $conn['usuario'], $conn['senha'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3,
        ]);

        // Testar uma consulta simples
        $stmt = $testPdo->query("SELECT 1 as teste");
        $resultado = $stmt->fetch();

        return [
            'sucesso' => true,
            'ambiente' => $ambiente,
            'host' => $conn['host'],
            'banco' => $conn['banco'],
            'teste_query' => $resultado['teste'] == 1
        ];
    } catch (Exception $e) {
        return [
            'sucesso' => false,
            'ambiente' => $ambiente,
            'erro' => $e->getMessage()
        ];
    }
}

try {
    $configPath = __DIR__ . '/config/database_simples.php';
    if (!file_exists($configPath)) {
        echo "❌ Arquivo de configuração não encontrado: {$configPath}\n";
        exit(1);
    }

    $config = require $configPath;

    if ($comando === 'status') {
        echo "📊 STATUS ATUAL DO SISTEMA\n";
        echo "═══════════════════════════\n\n";

        $ambienteAtual = $config['deteccao_auto'] ? detectarAmbiente() : $config['ambiente'];

        echo "🎯 Ambiente Atual: " . strtoupper($ambienteAtual) . "\n";
        echo "🔧 Detecção Automática: " . ($config['deteccao_auto'] ? 'ATIVADA' : 'DESATIVADA') . "\n";
        echo "🖥️ Hostname: " . gethostname() . "\n";
        echo "📁 Diretório: " . getcwd() . "\n\n";

        if (isset($config['conexoes'][$ambienteAtual])) {
            $conn = $config['conexoes'][$ambienteAtual];
            echo "🔗 Conexão Atual:\n";
            echo "   Host: {$conn['host']}:{$conn['porta']}\n";
            echo "   Banco: {$conn['banco']}\n";
            echo "   Usuário: {$conn['usuario']}\n\n";
        }

        // Testar todas as conexões
        echo "🧪 TESTE DE CONEXÕES:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━\n";

        foreach (['desenvolvimento', 'homologacao', 'producao'] as $amb) {
            $teste = testarConexao($config, $amb);
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
        if (!empty($config['historico'])) {
            echo "\n📝 HISTÓRICO DE MUDANÇAS (últimas 5):\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

            $historico = array_slice($config['historico'], -5);
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

        $ambienteAtual = $config['deteccao_auto'] ? detectarAmbiente() : $config['ambiente'];

        if ($ambienteAtual === $novoAmbiente && !$config['deteccao_auto'] && $config['ambiente'] === $novoAmbiente) {
            echo "ℹ️ O ambiente já está configurado como " . strtoupper($novoAmbiente) . "\n";
            echo "✅ Nenhuma alteração necessária.\n";
        } else {
            echo "📋 Ambiente atual: " . strtoupper($ambienteAtual) . "\n";
            echo "🎯 Novo ambiente: " . strtoupper($novoAmbiente) . "\n\n";

            // Testar conexão antes de alterar
            echo "🧪 Testando conexão com o novo ambiente...\n";
            $teste = testarConexao($config, $novoAmbiente);

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

            // Adicionar ao histórico
            $config['historico'][] = [
                'ambiente_anterior' => $ambienteAtual,
                'ambiente_novo' => $novoAmbiente,
                'data_mudanca' => date('Y-m-d H:i:s'),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ];

            // Limitar histórico a 10 entradas
            if (count($config['historico']) > 10) {
                $config['historico'] = array_slice($config['historico'], -10);
            }

            $config['ambiente'] = $novoAmbiente;
            $config['deteccao_auto'] = false; // Desativar detecção automática quando alterado manualmente

            // Gerar novo conteúdo do arquivo
            $content = "<?php\n// Configuração Simplificada de Banco de Dados\n";
            $content .= "// Última atualização: " . date('Y-m-d H:i:s') . "\n\n";
            $content .= "return " . var_export($config, true) . ";\n";

            if (file_put_contents($configPath, $content)) {
                echo "✅ AMBIENTE ALTERADO COM SUCESSO!\n\n";

                $conexaoNova = $config['conexoes'][$novoAmbiente];
                echo "📊 Nova configuração ativa:\n";
                echo "   Ambiente: " . strtoupper($novoAmbiente) . "\n";
                echo "   Host: {$conexaoNova['host']}:{$conexaoNova['porta']}\n";
                echo "   Banco: {$conexaoNova['banco']}\n";
                echo "   Usuário: {$conexaoNova['usuario']}\n\n";

                echo "⚠️ IMPORTANTE:\n";
                echo "   • Reinicie a aplicação web para aplicar as mudanças\n";
                echo "   • Limpe qualquer cache da aplicação\n";
                echo "   • Execute 'php ambiente.php status' para verificar\n";
            } else {
                echo "❌ Erro ao alterar ambiente - falha ao salvar arquivo.\n";
                exit(1);
            }
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🏁 Operação concluída: " . date('Y-m-d H:i:s') . "\n";
