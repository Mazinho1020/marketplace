<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RESTAURAÇÃO DO BANCO DE DADOS ===\n\n";

try {
    // Ler o arquivo SQL
    $sqlFile = 'backup_restore.sql';

    if (!file_exists($sqlFile)) {
        echo "❌ Arquivo backup_restore.sql não encontrado!\n";
        exit(1);
    }

    $sql = file_get_contents($sqlFile);

    if (!$sql) {
        echo "❌ Erro ao ler o arquivo SQL!\n";
        exit(1);
    }

    echo "✅ Arquivo SQL carregado: " . number_format(strlen($sql)) . " caracteres\n";

    // Dividir o SQL em comandos individuais
    $commands = explode(';', $sql);
    $totalCommands = count($commands);

    echo "✅ Total de comandos SQL: $totalCommands\n\n";

    $success = 0;
    $errors = 0;

    foreach ($commands as $index => $command) {
        $command = trim($command);

        // Pular comandos vazios ou comentários
        if (empty($command) || strpos($command, '#') === 0 || strpos($command, '--') === 0) {
            continue;
        }

        try {
            DB::statement($command);
            $success++;

            if ($success % 10 == 0) {
                echo "✅ Comandos executados: $success\n";
            }
        } catch (Exception $e) {
            $errors++;
            echo "❌ Erro no comando " . ($index + 1) . ": " . $e->getMessage() . "\n";

            // Mostrar apenas os primeiros 100 caracteres do comando com erro
            $shortCommand = substr($command, 0, 100) . '...';
            echo "   Comando: $shortCommand\n\n";
        }
    }

    echo "\n=== RESULTADO DA RESTAURAÇÃO ===\n";
    echo "✅ Comandos executados com sucesso: $success\n";
    echo "❌ Comandos com erro: $errors\n";

    if ($errors == 0) {
        echo "\n🎉 Restauração concluída com SUCESSO!\n";
    } else {
        echo "\n⚠️ Restauração concluída com alguns erros.\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO CRÍTICO: " . $e->getMessage() . "\n";
}
