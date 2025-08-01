<?php

require_once __DIR__ . '/vendor/autoload.php';

// Carregar configurações Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTE DE CONFIGURAÇÃO DE BANCO ===\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "DB_DATABASE do .env: " . env('DB_DATABASE') . "\n";

try {
    $databaseName = DB::connection()->getDatabaseName();
    echo "Banco atual conectado: " . $databaseName . "\n";

    if (str_contains($databaseName, 'finanp06_')) {
        echo "❌ PROBLEMA: Conectado ao banco ONLINE!\n";
    } else {
        echo "✅ OK: Conectado ao banco LOCAL!\n";
    }

    // Verificar se as tabelas de configuração existem
    if (DB::getSchemaBuilder()->hasTable('config_environments')) {
        echo "✅ Tabela config_environments existe\n";

        $environments = DB::table('config_environments')->get();
        echo "Ambientes disponíveis:\n";
        foreach ($environments as $env) {
            echo "  - ID: {$env->id}, Código: {$env->codigo}, Nome: {$env->nome}\n";
        }

        if (DB::getSchemaBuilder()->hasTable('config_db_connections')) {
            echo "✅ Tabela config_db_connections existe\n";

            $connections = DB::table('config_db_connections')->get();
            echo "Conexões configuradas:\n";
            foreach ($connections as $conn) {
                echo "  - ID: {$conn->id}, Nome: {$conn->nome}, Ambiente: {$conn->ambiente_id}, Banco: {$conn->banco}\n";
            }
        } else {
            echo "❌ Tabela config_db_connections NÃO existe\n";
        }
    } else {
        echo "❌ Tabela config_environments NÃO existe\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
