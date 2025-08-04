<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ANÁLISE COMPLETA DO BANCO DE DADOS ===\n\n";

    // 1. Verificar tabelas existentes no banco
    echo "1. Verificando tabelas existentes no banco...\n";
    $tables = DB::select('SHOW TABLES');
    $existingTables = [];

    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $existingTables[] = $tableName;
    }

    echo "Total de tabelas existentes: " . count($existingTables) . "\n\n";

    // 2. Extrair tabelas do arquivo SQL
    echo "2. Extraindo tabelas do arquivo SQL...\n";
    $sqlFile = 'C:\\Users\\leoma\\Downloads\\teste2.sql';

    if (file_exists($sqlFile)) {
        $sqlContent = file_get_contents($sqlFile);

        // Extrair nomes das tabelas usando regex
        preg_match_all('/CREATE TABLE.*?`([^`]+)`/i', $sqlContent, $matches);
        $sqlTables = array_unique($matches[1]);

        echo "Total de tabelas no arquivo SQL: " . count($sqlTables) . "\n";

        // 3. Verificar quais tabelas estão faltando
        echo "\n3. Tabelas que estão FALTANDO no banco:\n";
        $missingTables = [];

        foreach ($sqlTables as $table) {
            if (!in_array($table, $existingTables)) {
                $missingTables[] = $table;
                echo "- " . $table . "\n";
            }
        }

        echo "\nTotal de tabelas faltantes: " . count($missingTables) . "\n";

        // 4. Listar tabelas existentes  
        echo "\n4. Tabelas que JÁ EXISTEM no banco:\n";
        $existingFromSql = [];

        foreach ($sqlTables as $table) {
            if (in_array($table, $existingTables)) {
                $existingFromSql[] = $table;
                echo "- " . $table . "\n";
            }
        }

        echo "\nTotal de tabelas já criadas: " . count($existingFromSql) . "\n";

        // 5. Resumo final
        echo "\n=== RESUMO FINAL ===\n";
        echo "📊 Tabelas no arquivo SQL: " . count($sqlTables) . "\n";
        echo "✅ Tabelas já criadas: " . count($existingFromSql) . "\n";
        echo "❌ Tabelas faltantes: " . count($missingTables) . "\n";
        echo "📈 Progresso: " . round((count($existingFromSql) / count($sqlTables)) * 100, 1) . "%\n";

        if (count($missingTables) > 0) {
            echo "\n🔧 Deseja criar as tabelas faltantes? Execute:\n";
            echo "php create_all_missing_tables.php\n";

            // Salvar lista de tabelas faltantes
            file_put_contents('missing_tables.txt', implode("\n", $missingTables));
            echo "\n📝 Lista salva em: missing_tables.txt\n";
        } else {
            echo "\n🎉 Todas as tabelas estão criadas!\n";
        }
    } else {
        echo "❌ Arquivo SQL não encontrado: {$sqlFile}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
