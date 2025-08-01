<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RESTAURANDO BACKUP COMPLETO ===\n";

try {
    // Ler o arquivo SQL
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

    echo "💾 Arquivo lido com sucesso (" . strlen($sql) . " bytes)\n";

    // Dividir em comandos separados
    $commands = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($cmd) {
            return !empty($cmd) && !preg_match('/^#|^--/', $cmd);
        }
    );

    echo "🔄 Executando " . count($commands) . " comandos SQL...\n\n";

    $successful = 0;
    $errors = 0;

    foreach ($commands as $index => $command) {
        try {
            // Pular comentários e linhas vazias
            if (empty(trim($command)) || preg_match('/^(#|--|\s*$)/', $command)) {
                continue;
            }

            DB::statement($command);
            $successful++;

            // Mostrar progresso a cada 50 comandos
            if ($successful % 50 == 0) {
                echo "✅ $successful comandos executados...\n";
            }
        } catch (Exception $e) {
            $errors++;
            echo "❌ Erro no comando " . ($index + 1) . ": " . $e->getMessage() . "\n";

            // Mostrar apenas os primeiros 10 erros para não poluir
            if ($errors > 10) {
                echo "... (suprimindo mais erros)\n";
                break;
            }
        }
    }

    echo "\n=== RESULTADO ===\n";
    echo "✅ Comandos executados com sucesso: $successful\n";
    echo "❌ Comandos com erro: $errors\n";

    // Verificar tabelas após restauração
    echo "\n=== VERIFICANDO TABELAS ===\n";
    $tables = DB::select('SHOW TABLES');
    echo "📊 Total de tabelas: " . count($tables) . "\n";

    echo "\nTabelas com 'fidelidade' no nome:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        if (strpos($tableName, 'fidelidade') !== false) {
            echo "- $tableName\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro geral: " . $e->getMessage() . "\n";
}
