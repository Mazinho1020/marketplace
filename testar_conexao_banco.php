<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 TESTE DE CONEXÃO COM BANCO DE DADOS\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    // Testar conexão
    echo "⚡ Testando conexão...\n";
    DB::connection()->getPdo();
    echo "✅ Conexão com banco OK!\n\n";

    // Verificar qual banco está sendo usado
    $database = DB::connection()->getDatabaseName();
    echo "📁 Banco atual: {$database}\n\n";

    // Listar tabelas
    echo "📋 TABELAS ENCONTRADAS:\n";
    $tables = DB::select('SHOW TABLES');
    $tableCount = 0;
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "   - {$tableName}\n";
        $tableCount++;
    }
    echo "\n📊 Total: {$tableCount} tabelas\n\n";

    // Verificar se existem tabelas importantes
    $importantTables = [
        'empresa_usuarios' => 'Usuários comerciantes',
        'empresas' => 'Empresas',
        'empresa_horarios_funcionamento' => 'Horários de funcionamento'
    ];

    echo "🔑 TABELAS IMPORTANTES:\n";
    foreach ($importantTables as $table => $description) {
        $exists = DB::select("SHOW TABLES LIKE '{$table}'");
        $icon = !empty($exists) ? "✅" : "❌";
        echo "   {$icon} {$table} ({$description})\n";

        if (!empty($exists)) {
            // Contar registros
            $count = DB::table($table)->count();
            echo "      📊 Registros: {$count}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO DE CONEXÃO:\n";
    echo "   Mensagem: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";

    echo "🔧 SOLUÇÕES:\n";
    echo "   1. Verificar se o XAMPP está rodando\n";
    echo "   2. Verificar se o MySQL está ativo\n";
    echo "   3. Verificar as configurações do .env\n";
    echo "   4. Verificar se o banco 'meufinanceiro' existe\n";
}

echo "\n" . str_repeat("=", 42) . "\n";
