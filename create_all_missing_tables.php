<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CRIANDO TODAS AS TABELAS FALTANTES ===\n\n";

    // 1. Ler arquivo SQL
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2.sql';

    if (!file_exists($sqlFile)) {
        echo "❌ Arquivo não encontrado: {$sqlFile}\n";
        exit;
    }

    echo "📖 Lendo arquivo SQL...\n";
    $sqlContent = file_get_contents($sqlFile);

    // 2. Extrair todas as estruturas CREATE TABLE
    preg_match_all('/-- Copiando estrutura.*?CREATE TABLE.*?;/s', $sqlContent, $tableStructures);

    echo "📊 Encontradas " . count($tableStructures[0]) . " estruturas de tabela\n\n";

    $created = 0;
    $errors = 0;
    $skipped = 0;

    foreach ($tableStructures[0] as $structure) {
        // Extrair nome da tabela
        if (preg_match('/CREATE TABLE.*?`([^`]+)`/i', $structure, $matches)) {
            $tableName = $matches[1];

            try {
                // Verificar se tabela já existe
                $exists = DB::select("SHOW TABLES LIKE '{$tableName}'");

                if (!empty($exists)) {
                    echo "⏭️  Pulando (já existe): {$tableName}\n";
                    $skipped++;
                    continue;
                }

                // Extrair apenas o comando CREATE TABLE
                if (preg_match('/CREATE TABLE.*?;/s', $structure, $createMatch)) {
                    $createStatement = $createMatch[0];

                    // Limpar e ajustar o comando SQL
                    $createStatement = str_replace('CHECK (json_valid(`credenciais`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`configuracoes`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`recursos`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`limites`))', '', $createStatement);
                    $createStatement = str_replace('CHECK (json_valid(`metadados`))', '', $createStatement);

                    // Executar criação da tabela
                    DB::statement($createStatement);

                    echo "✅ Criada: {$tableName}\n";
                    $created++;
                }
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();

                // Se for erro de tabela já existe, pular
                if (strpos($errorMsg, 'already exists') !== false) {
                    echo "ℹ️  Já existe: {$tableName}\n";
                    $skipped++;
                } else {
                    echo "❌ Erro {$tableName}: " . substr($errorMsg, 0, 100) . "...\n";
                    $errors++;
                }
            }
        }
    }

    echo "\n=== RESUMO FINAL ===\n";
    echo "✅ Tabelas criadas: {$created}\n";
    echo "⏭️  Tabelas puladas: {$skipped}\n";
    echo "❌ Erros: {$errors}\n";
    echo "📊 Total processado: " . ($created + $skipped + $errors) . "\n";

    if ($errors === 0) {
        echo "\n🎉 Processo concluído com sucesso!\n";
        echo "🔍 Execute 'php analyze_all_tables.php' para verificar o resultado\n";
    } else {
        echo "\n⚠️  Processo concluído com alguns erros. Verifique os logs acima.\n";
    }
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
