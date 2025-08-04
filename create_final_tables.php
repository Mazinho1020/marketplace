<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CRIANDO AS 17 TABELAS FALTANTES (MÉTODO DIRETO) ===\n\n";

    // Lista das 17 tabelas que precisam ser criadas
    $failedTables = [
        'empresa_cache',
        'empresa_certificados',
        'empresa_cnaes',
        'empresa_config_seguranca',
        'empresa_logs_permissoes',
        'empresa_papeis',
        'empresa_papel_permissoes',
        'empresa_permissoes',
        'empresa_socios',
        'empresa_usuarios_activity_log',
        'empresa_usuarios_remember_tokens',
        'empresa_usuarios_security_settings',
        'empresa_usuario_empresas',
        'empresa_usuario_papeis',
        'empresa_usuario_permissoes',
        'login',
        'produto_importar'
    ];

    // Ler arquivo SQL completo
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2.sql';
    $sqlContent = file_get_contents($sqlFile);

    // Dividir o arquivo em linhas e processar
    $lines = explode("\n", $sqlContent);
    $tableStructures = [];
    $currentTable = null;
    $currentSQL = [];
    $inCreateTable = false;

    echo "📖 Extraindo estruturas das tabelas...\n";

    foreach ($lines as $line) {
        $line = trim($line);

        // Detectar início de CREATE TABLE (com ou sem IF NOT EXISTS)
        if (preg_match('/CREATE TABLE(?:\s+IF NOT EXISTS)?\s+`([^`]+)`/', $line, $matches)) {
            $tableName = $matches[1];
            if (in_array($tableName, $failedTables)) {
                $currentTable = $tableName;
                $currentSQL = [$line];
                $inCreateTable = true;
                echo "🔍 Encontrada estrutura para: {$tableName}\n";
            }
        }

        // Se estamos dentro de um CREATE TABLE que nos interessa
        if ($inCreateTable && $currentTable) {
            if ($currentTable && !in_array($line, $currentSQL)) {
                $currentSQL[] = $line;
            }

            // Detectar fim do CREATE TABLE
            if (preg_match('/\).*;$/', $line)) {
                $tableStructures[$currentTable] = implode("\n", $currentSQL);
                $inCreateTable = false;
                $currentTable = null;
                $currentSQL = [];
            }
        }
    }

    echo "\n📊 Estruturas extraídas: " . count($tableStructures) . "\n\n";

    $created = 0;
    $errors = 0;

    // Tentar criar cada tabela
    foreach ($failedTables as $tableName) {
        echo "🔧 Criando: {$tableName}\n";

        try {
            // Verificar se já existe
            $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($exists)) {
                echo "ℹ️  Já existe: {$tableName}\n\n";
                continue;
            }

            if (!isset($tableStructures[$tableName])) {
                echo "❌ Estrutura não encontrada para: {$tableName}\n\n";
                $errors++;
                continue;
            }

            $createStatement = $tableStructures[$tableName];

            // Limpeza do SQL - remover problemas comuns
            $createStatement = str_replace('CHECK (json_valid(`credenciais`))', '', $createStatement);
            $createStatement = str_replace('CHECK (json_valid(`configuracoes`))', '', $createStatement);
            $createStatement = str_replace('CHECK (json_valid(`recursos`))', '', $createStatement);
            $createStatement = str_replace('CHECK (json_valid(`limites`))', '', $createStatement);
            $createStatement = str_replace('CHECK (json_valid(`metadados`))', '', $createStatement);

            // Remover foreign keys temporariamente para evitar problemas de dependência
            $createStatement = preg_replace('/,\s*CONSTRAINT[^,]+FOREIGN KEY[^,]+REFERENCES[^,)]+\)/i', '', $createStatement);
            $createStatement = preg_replace('/,\s*FOREIGN KEY[^,)]+\)/i', '', $createStatement);

            // Limpar vírgulas e espaços extras
            $createStatement = preg_replace('/,\s*,/', ',', $createStatement);
            $createStatement = preg_replace('/,\s*\)/', ')', $createStatement);
            $createStatement = preg_replace('/\s+/', ' ', $createStatement);

            // Executar criação sem transação para evitar rollback
            DB::unprepared($createStatement);

            // Verificar se foi realmente criada
            $verification = DB::select("SHOW TABLES LIKE '{$tableName}'");
            if (!empty($verification)) {
                echo "✅ Criada e verificada: {$tableName}\n";
                $created++;
            } else {
                echo "❌ Falha na criação: {$tableName}\n";
                $errors++;
            }
        } catch (Exception $e) {
            $errorMsg = substr($e->getMessage(), 0, 100);
            echo "❌ Erro {$tableName}: {$errorMsg}...\n";
            echo "SQL usado: " . substr($createStatement ?? 'N/A', 0, 100) . "...\n";
            $errors++;
        }

        echo "\n";
    }

    echo "=== RESUMO FINAL ===\n";
    echo "✅ Tabelas criadas: {$created}\n";
    echo "❌ Erros: {$errors}\n";
    echo "📊 Total tentativas: " . count($failedTables) . "\n";

    if ($created > 0) {
        echo "\n🎉 Sucesso! {$created} tabelas foram criadas!\n";
    }

    if ($errors > 0) {
        echo "\n⚠️  Tabelas com problemas: {$errors}\n";
    }

    echo "\n🔍 Executando verificação final...\n";

    // Verificação final de todas as tabelas
    $finalCheck = 0;
    foreach ($failedTables as $tableName) {
        $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");
        if (!empty($exists)) {
            $finalCheck++;
        }
    }

    echo "📊 Verificação final: {$finalCheck} de " . count($failedTables) . " tabelas existem no banco\n";
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
