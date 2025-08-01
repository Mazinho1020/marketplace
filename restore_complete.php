<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RESTAURAÇÃO COMPLETA - VERSÃO ROBUSTA ===\n";

try {
    $sqlFile = 'meufinanceirotabelas completas .sql';

    if (!file_exists($sqlFile)) {
        echo "❌ Arquivo não encontrado: $sqlFile\n";
        exit(1);
    }

    echo "📂 Lendo arquivo de backup...\n";
    $sql = file_get_contents($sqlFile);

    if (!$sql) {
        echo "❌ Erro ao ler o arquivo\n";
        exit(1);
    }

    // Remover BOM se existir
    $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);

    echo "💾 Arquivo lido (" . strlen($sql) . " bytes)\n";
    echo "🔧 Processando comandos SQL...\n";

    // Dividir por pontos e vírgulas, mas sendo mais inteligente
    $lines = explode("\n", $sql);
    $currentCommand = '';
    $commands = [];
    $inInsert = false;

    foreach ($lines as $line) {
        $line = trim($line);

        // Pular comentários e linhas vazias
        if (empty($line) || preg_match('/^(#|--|\s*$)/', $line)) {
            continue;
        }

        $currentCommand .= ' ' . $line;

        // Detectar final de comando
        if (preg_match('/;\s*$/', $line)) {
            $cmd = trim($currentCommand);
            if (!empty($cmd) && !preg_match('/^(#|--|\s*$)/', $cmd)) {
                $commands[] = $cmd;
            }
            $currentCommand = '';
        }
    }

    echo "🎯 Encontrados " . count($commands) . " comandos para executar\n\n";

    $successful = 0;
    $errors = 0;
    $tableCount = 0;
    $skipped = 0;

    // Desabilitar verificação de chaves estrangeiras temporariamente
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    echo "🔓 Verificação de chaves estrangeiras desabilitada\n";

    foreach ($commands as $index => $command) {
        try {
            $command = trim($command);

            // Pular comandos vazios ou apenas comentários
            if (empty($command) || preg_match('/^(#|--|\s*$)/', $command)) {
                $skipped++;
                continue;
            }

            // Executar comando
            DB::statement($command);
            $successful++;

            // Contar tabelas criadas
            if (preg_match('/CREATE TABLE.*`([^`]+)`/i', $command, $matches)) {
                $tableCount++;
                echo "✅ Tabela criada: {$matches[1]}\n";
            }

            // Mostrar progresso a cada 25 comandos
            if ($successful % 25 == 0) {
                echo "📊 Progresso: $successful comandos executados...\n";
            }
        } catch (Exception $e) {
            $errors++;
            $errorMsg = $e->getMessage();

            // Categorizar erros
            if (strpos($errorMsg, 'already exists') !== false) {
                // Tabela já existe - não é erro crítico
                if (preg_match('/Table \'([^\']+)\' already exists/', $errorMsg, $matches)) {
                    echo "⚠️  Tabela já existe: {$matches[1]} (pulando)\n";
                }
            } elseif (strpos($errorMsg, 'Foreign key constraint') !== false) {
                echo "⚠️  Erro de chave estrangeira (pulando): " . substr($errorMsg, 0, 100) . "...\n";
            } elseif (strpos($errorMsg, 'Syntax error') !== false) {
                echo "⚠️  Erro de sintaxe (pulando): " . substr($errorMsg, 0, 100) . "...\n";
            } else {
                echo "❌ Erro no comando " . ($index + 1) . ": " . substr($errorMsg, 0, 150) . "...\n";
            }

            // Parar apenas se houver muitos erros críticos seguidos
            if ($errors > 50) {
                echo "🚫 Muitos erros encontrados. Parando execução.\n";
                break;
            }
        }
    }

    // Reabilitar verificação de chaves estrangeiras
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    echo "🔒 Verificação de chaves estrangeiras reabilitada\n\n";

    echo "=== RESULTADO FINAL ===\n";
    echo "✅ Comandos executados: $successful\n";
    echo "⚠️  Comandos com erro: $errors\n";
    echo "⏭️  Comandos pulados: $skipped\n";
    echo "🏗️  Tabelas criadas: $tableCount\n\n";

    // Verificar resultado final
    echo "=== VERIFICAÇÃO FINAL ===\n";
    $tables = DB::select('SHOW TABLES');
    echo "📊 Total de tabelas no banco: " . count($tables) . "\n\n";

    // Listar todas as tabelas
    echo "📋 LISTA COMPLETA DE TABELAS:\n";
    $tableNames = [];
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $tableNames[] = $tableName;
        echo "- $tableName\n";
    }

    // Verificar tabelas específicas importantes
    echo "\n🎯 VERIFICAÇÃO DE TABELAS IMPORTANTES:\n";
    $importantTables = [
        'empresa_usuarios',
        'produtos',
        'vendas',
        'clientes',
        'fornecedores',
        'categoria_produto',
        'movimento_estoque',
        'conta_gerencial',
        'fidelidade_carteiras',
        'fidelidade_cashback_regras',
        'config'
    ];

    foreach ($importantTables as $table) {
        if (in_array($table, $tableNames)) {
            echo "✅ $table - EXISTE\n";
        } else {
            echo "❌ $table - NÃO EXISTE\n";
        }
    }

    echo "\n🎉 RESTAURAÇÃO CONCLUÍDA!\n";
    echo "🔧 Se ainda faltam tabelas, pode ser necessário executar novamente para resolver dependências.\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}
