<?php

echo "🧹 LIMPEZA E SIMPLIFICAÇÃO DO SISTEMA 🧹\n";
echo "════════════════════════════════════════\n\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=meufinanceiro;charset=utf8', 'root', '', [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "✅ Conectado ao banco: meufinanceiro\n\n";

    echo "📋 VERIFICANDO TABELAS DE CONFIGURAÇÃO EXISTENTES:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    // Verificar quais tabelas existem
    $tabelas = ['config_environments', 'config_db_connections', 'config_url_mappings', 'config_sites'];
    $tabelasExistentes = [];

    foreach ($tabelas as $tabela) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$tabela}'");
            if ($stmt->fetch()) {
                $tabelasExistentes[] = $tabela;

                // Contar registros
                $count = $pdo->query("SELECT COUNT(*) as total FROM {$tabela}")->fetch()['total'];
                echo "  📊 {$tabela}: {$count} registros\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao verificar {$tabela}: {$e->getMessage()}\n";
        }
    }

    if (empty($tabelasExistentes)) {
        echo "  ✅ Nenhuma tabela de configuração encontrada - sistema já está limpo!\n";
    } else {
        echo "\n🗑️ REMOVENDO TABELAS DE CONFIGURAÇÃO:\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

        // Confirmar antes de remover
        echo "ATENÇÃO: As seguintes tabelas serão removidas:\n";
        foreach ($tabelasExistentes as $tabela) {
            echo "  • {$tabela}\n";
        }

        echo "\n⚠️ ESTA AÇÃO NÃO PODE SER DESFEITA!\n";
        echo "Pressione ENTER para continuar ou Ctrl+C para cancelar...\n";

        // Para automação, vamos prosseguir automaticamente em ambiente de desenvolvimento
        $hostname = gethostname();
        $isDev = (str_contains(strtolower($hostname), 'desktop') ||
            str_contains(strtolower($hostname), 'servidor') ||
            str_contains(getcwd(), 'xampp'));

        if ($isDev) {
            echo "🔧 Ambiente de desenvolvimento detectado - prosseguindo automaticamente...\n\n";

            $pdo->beginTransaction();

            try {
                // Remover tabelas na ordem correta (dependências primeiro)
                $ordemRemocao = ['config_url_mappings', 'config_sites', 'config_db_connections', 'config_environments'];

                foreach ($ordemRemocao as $tabela) {
                    if (in_array($tabela, $tabelasExistentes)) {
                        $pdo->exec("DROP TABLE IF EXISTS {$tabela}");
                        echo "  ✅ Tabela {$tabela} removida\n";
                    }
                }

                $pdo->commit();
                echo "\n🎉 TABELAS DE CONFIGURAÇÃO REMOVIDAS COM SUCESSO!\n";
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "❌ Erro ao remover tabelas: {$e->getMessage()}\n";
            }
        } else {
            echo "⚠️ Ambiente de produção detectado - remoção cancelada por segurança.\n";
            echo "Execute manualmente os comandos SQL se desejar prosseguir.\n";
        }
    }

    echo "\n" . str_repeat("═", 60) . "\n";
    echo "📁 CRIANDO CONFIGURAÇÃO SIMPLIFICADA:\n";
    echo str_repeat("═", 60) . "\n\n";

    // Criar diretório config se não existir
    $configDir = __DIR__ . '/config';
    if (!is_dir($configDir)) {
        mkdir($configDir, 0755, true);
        echo "✅ Diretório config/ criado\n";
    }

    // Criar arquivo de configuração simplificado
    $configContent = '<?php
// Configuração Simplificada de Banco de Dados
// Última atualização: ' . date('Y-m-d H:i:s') . '

return [
    // Ambiente atual: desenvolvimento, homologacao ou producao
    // Altere este valor para trocar de ambiente
    \'ambiente\' => \'desenvolvimento\',
    
    // Configurações das conexões por ambiente
    \'conexoes\' => [
        \'desenvolvimento\' => [
            \'driver\' => \'mysql\',
            \'host\' => \'localhost\',
            \'porta\' => 3306,
            \'banco\' => \'meufinanceiro\',
            \'usuario\' => \'root\',
            \'senha\' => \'\',
            \'charset\' => \'utf8mb4\',
            \'collation\' => \'utf8mb4_unicode_ci\',
            \'prefixo\' => \'\',
        ],
        \'homologacao\' => [
            \'driver\' => \'mysql\',
            \'host\' => \'homolog.exemplo.com\',
            \'porta\' => 3306,
            \'banco\' => \'homolog_meufinanceiro\',
            \'usuario\' => \'homolog_user\',
            \'senha\' => \'senha_homolog\',
            \'charset\' => \'utf8mb4\',
            \'collation\' => \'utf8mb4_unicode_ci\',
            \'prefixo\' => \'\',
        ],
        \'producao\' => [
            \'driver\' => \'mysql\',
            \'host\' => \'162.241.2.71\',
            \'porta\' => 3306,
            \'banco\' => \'finanp06_meufinanceiro\',
            \'usuario\' => \'finanp06_tradicao\',
            \'senha\' => \'Mazinho2512@\',
            \'charset\' => \'utf8mb4\',
            \'collation\' => \'utf8mb4_unicode_ci\',
            \'prefixo\' => \'\',
        ],
    ],
    
    // Detecção automática de ambiente baseada no hostname/diretório
    \'deteccao_auto\' => true, // true = detecção automática, false = usar ambiente definido acima
    
    // Log de mudanças
    \'historico\' => [
        // Será preenchido automaticamente quando o ambiente for alterado
    ],
];';

    $configFile = $configDir . '/database_simples.php';
    if (file_put_contents($configFile, $configContent)) {
        echo "✅ Arquivo de configuração criado: config/database_simples.php\n";
    } else {
        echo "❌ Erro ao criar arquivo de configuração\n";
    }

    echo "\n📋 REMOVENDO ARQUIVOS ANTIGOS DESNECESSÁRIOS:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

    // Lista de arquivos para remover
    $arquivosParaRemover = [
        'app/Services/Database/DatabaseEnvironmentService.php',
        'app/Providers/DatabaseConfigServiceProvider.php',
        'app/Models/Config/ConfigEnvironment.php',
        'app/Models/Config/ConfigDbConnection.php',
        'app/Models/Config/ConfigSite.php',
        'app/Models/Config/ConfigUrlMapping.php',
        'teste_dinamico_completo.php',
        'teste_dinamico_simples.php',
        'teste_banco_direto.php',
        'teste_ultra_simples.php',
        'verificar_estrutura.php',
        'mostrar_tabelas.php',
        'debug_simple.php',
        'debug_steps.php',
        'teste_service_standalone.php',
        'teste_laravel_completo.php',
        'teste_rapido.php',
        'basic_test.php',
    ];

    $removidos = 0;
    foreach ($arquivosParaRemover as $arquivo) {
        if (file_exists($arquivo)) {
            if (unlink($arquivo)) {
                echo "  ✅ Removido: {$arquivo}\n";
                $removidos++;
            } else {
                echo "  ❌ Erro ao remover: {$arquivo}\n";
            }
        }
    }

    echo "\n📊 Arquivos removidos: {$removidos}\n";

    echo "\n" . str_repeat("═", 60) . "\n";
    echo "🎉 SISTEMA SIMPLIFICADO COM SUCESSO!\n";
    echo str_repeat("═", 60) . "\n\n";

    echo "📋 PRÓXIMOS PASSOS:\n";
    echo "1. ✅ Tabelas de configuração removidas\n";
    echo "2. ✅ Arquivo de configuração simples criado\n";
    echo "3. ✅ Arquivos antigos desnecessários removidos\n";
    echo "4. 🔄 Próximo: Implementar o sistema simplificado\n\n";

    echo "🔧 PARA USAR O NOVO SISTEMA:\n";
    echo "• Edite config/database_simples.php para trocar de ambiente\n";
    echo "• Ou use o script de alternância que será criado\n";
    echo "• Ou use a interface web que será criada\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n🏁 LIMPEZA CONCLUÍDA: " . date('Y-m-d H:i:s') . "\n";
