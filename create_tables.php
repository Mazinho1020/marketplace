<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Executando criação das tabelas faltantes...\n\n";

    // Ler o arquivo SQL
    $sql = file_get_contents('create_missing_tables.sql');

    // Dividir em comandos individuais
    $commands = explode(';', $sql);

    $created = 0;
    $errors = 0;

    foreach ($commands as $command) {
        $command = trim($command);

        // Pular comandos vazios ou comentários
        if (empty($command) || strpos($command, '--') === 0 || strpos($command, '/*') === 0) {
            continue;
        }

        try {
            DB::statement($command);

            // Verificar se é um CREATE TABLE
            if (preg_match('/CREATE TABLE.*`([^`]+)`/', $command, $matches)) {
                $tableName = $matches[1];
                echo "✅ Tabela criada: {$tableName}\n";
                $created++;
            }
        } catch (Exception $e) {
            // Verificar se é erro de tabela já existe
            if (strpos($e->getMessage(), 'already exists') !== false) {
                if (preg_match('/CREATE TABLE.*`([^`]+)`/', $command, $matches)) {
                    $tableName = $matches[1];
                    echo "ℹ️  Tabela já existe: {$tableName}\n";
                }
            } else {
                echo "❌ Erro: " . $e->getMessage() . "\n";
                $errors++;
            }
        }
    }

    echo "\n=== RESUMO ===\n";
    echo "Tabelas criadas: {$created}\n";
    echo "Erros: {$errors}\n";

    if ($errors === 0) {
        echo "\n🎉 Todas as tabelas foram criadas com sucesso!\n";
    }
} catch (Exception $e) {
    echo "Erro geral: " . $e->getMessage() . "\n";
}
