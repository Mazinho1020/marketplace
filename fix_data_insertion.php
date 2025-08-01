<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORREÇÃO DE DADOS E INSERÇÃO COMPLETA ===\n";

try {
    $sqlFile = 'meufinanceirotabelas completas .sql';

    if (!file_exists($sqlFile)) {
        echo "❌ Arquivo não encontrado: $sqlFile\n";
        exit(1);
    }

    echo "📂 Lendo arquivo de backup...\n";
    $sql = file_get_contents($sqlFile);

    // Remover BOM se existir
    $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql);

    echo "💾 Arquivo lido (" . strlen($sql) . " bytes)\n";
    echo "🔧 Processando comandos INSERT...\n";

    // Focar apenas nos comandos INSERT que falharam
    $lines = explode("\n", $sql);
    $insertCommands = [];
    $currentCommand = '';

    foreach ($lines as $line) {
        $line = trim($line);

        // Pular comentários e linhas vazias
        if (empty($line) || preg_match('/^(#|--|\s*$)/', $line)) {
            continue;
        }

        // Detectar início de INSERT
        if (preg_match('/^INSERT INTO/i', $line)) {
            if (!empty($currentCommand)) {
                $insertCommands[] = trim($currentCommand);
            }
            $currentCommand = $line;
        } else if (!empty($currentCommand)) {
            $currentCommand .= ' ' . $line;
        }

        // Detectar final de comando
        if (preg_match('/;\s*$/', $line) && !empty($currentCommand)) {
            $insertCommands[] = trim($currentCommand);
            $currentCommand = '';
        }
    }

    echo "🎯 Encontrados " . count($insertCommands) . " comandos INSERT\n\n";

    // Desabilitar verificações temporariamente
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    DB::statement('SET sql_mode = ""'); // Permitir datas inválidas e outros formatos
    echo "🔓 Verificações de integridade desabilitadas\n";

    $successful = 0;
    $errors = 0;
    $tableData = [];

    foreach ($insertCommands as $index => $command) {
        try {
            // Extrair nome da tabela
            if (preg_match('/INSERT INTO `?([^`\s]+)`?\s/i', $command, $matches)) {
                $tableName = $matches[1];

                // Focar nas tabelas importantes que podem ter falhado
                $importantTables = [
                    'produtos',
                    'lancamentos',
                    'funcionario_cargo',
                    'funcionario_depart',
                    'forma_pagamento_bandeiras',
                    'formas_pagamento',
                    'funforcli',
                    'empresa_usuarios',
                    'empresas',
                    'config_definitions'
                ];

                // Aplicar correções específicas por tabela
                $correctedCommand = $command;

                // Correção para datas inválidas
                $correctedCommand = preg_replace("/'0000-00-00'/", 'NULL', $correctedCommand);
                $correctedCommand = preg_replace("/'0000-00-00 00:00:00'/", 'NULL', $correctedCommand);

                // Correção para valores de enum que não existem
                if ($tableName === 'produtos') {
                    // Substituir valores de enum inválidos
                    $correctedCommand = preg_replace("/'situacao_estoque_invalida'/", "'ativo'", $correctedCommand);
                    $correctedCommand = preg_replace("/'status_invalido'/", "'ativo'", $correctedCommand);
                }

                if ($tableName === 'lancamentos') {
                    // Substituir valores de status PDV inválidos
                    $correctedCommand = preg_replace("/'status_pdv_invalido'/", "'pendente'", $correctedCommand);
                }

                if ($tableName === 'config_definitions') {
                    // Substituir valores de sync_status inválidos
                    $correctedCommand = preg_replace("/'sync_status_invalido'/", "'pendente'", $correctedCommand);
                }

                // Executar comando corrigido
                DB::statement($correctedCommand);
                $successful++;

                if (!isset($tableData[$tableName])) {
                    $tableData[$tableName] = 0;
                }
                $tableData[$tableName]++;

                // Mostrar progresso para tabelas importantes
                if (in_array($tableName, $importantTables)) {
                    echo "✅ Dados inseridos em: $tableName (registro #{$tableData[$tableName]})\n";
                }

                // Mostrar progresso geral
                if ($successful % 100 == 0) {
                    echo "📊 Progresso: $successful comandos INSERT executados...\n";
                }
            }
        } catch (Exception $e) {
            $errors++;
            $errorMsg = $e->getMessage();

            // Categorizar e tratar erros específicos
            if (strpos($errorMsg, 'Data truncated') !== false) {
                echo "⚠️  Dados truncados (corrigindo): " . substr($errorMsg, 0, 80) . "...\n";

                // Tentar uma versão mais permissiva
                try {
                    $relaxedCommand = preg_replace("/'[^']*situacao_estoque[^']*'/", "'ativo'", $command);
                    $relaxedCommand = preg_replace("/'[^']*status_pdv[^']*'/", "'pendente'", $relaxedCommand);
                    DB::statement($relaxedCommand);
                    $successful++;
                    echo "✅ Comando corrigido executado com sucesso\n";
                } catch (Exception $e2) {
                    echo "❌ Falha mesmo após correção: " . substr($e2->getMessage(), 0, 100) . "...\n";
                }
            } elseif (strpos($errorMsg, 'Duplicate entry') !== false) {
                echo "⚠️  Entrada duplicada (pulando): " . substr($errorMsg, 0, 80) . "...\n";
            } elseif (strpos($errorMsg, 'Incorrect date') !== false) {
                echo "⚠️  Data inválida (pulando): " . substr($errorMsg, 0, 80) . "...\n";
            } else {
                echo "❌ Erro INSERT: " . substr($errorMsg, 0, 100) . "...\n";
            }

            // Parar se houver muitos erros
            if ($errors > 100) {
                echo "🚫 Muitos erros encontrados. Parando execução.\n";
                break;
            }
        }
    }

    // Reabilitar verificações
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    DB::statement('SET sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO"');
    echo "🔒 Verificações de integridade reabilitadas\n\n";

    echo "=== RESULTADO DA INSERÇÃO ===\n";
    echo "✅ Comandos INSERT executados: $successful\n";
    echo "❌ Comandos com erro: $errors\n\n";

    echo "📊 DADOS INSERIDOS POR TABELA:\n";
    arsort($tableData);
    foreach ($tableData as $table => $count) {
        echo "- $table: $count registros\n";
    }

    // Verificar contagem de registros nas tabelas importantes
    echo "\n🎯 VERIFICAÇÃO DE DADOS NAS TABELAS IMPORTANTES:\n";
    $importantTables = ['produtos', 'lancamentos', 'empresa_usuarios', 'clientes', 'formas_pagamento'];

    foreach ($importantTables as $table) {
        try {
            $count = DB::selectOne("SELECT COUNT(*) as total FROM `$table`");
            echo "✅ $table: {$count->total} registros\n";
        } catch (Exception $e) {
            echo "❌ $table: Erro ao contar registros\n";
        }
    }

    echo "\n🎉 INSERÇÃO DE DADOS CONCLUÍDA!\n";
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}
