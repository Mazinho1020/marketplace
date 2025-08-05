<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 ESTRUTURA DAS TABELAS\n";
echo "=" . str_repeat("=", 25) . "\n\n";

try {
    // Estrutura da tabela empresas
    echo "🏢 TABELA: empresas\n";
    $columns = DB::select('DESCRIBE empresas');
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }

    echo "\n👥 TABELA: empresa_usuarios\n";
    $columns = DB::select('DESCRIBE empresa_usuarios');
    foreach ($columns as $col) {
        echo "   - {$col->Field} ({$col->Type})\n";
    }

    echo "\n🔗 TABELA: empresa_user_vinculos\n";
    $exists = DB::select("SHOW TABLES LIKE 'empresa_user_vinculos'");
    if (!empty($exists)) {
        $columns = DB::select('DESCRIBE empresa_user_vinculos');
        foreach ($columns as $col) {
            echo "   - {$col->Field} ({$col->Type})\n";
        }
    } else {
        echo "   ❌ Tabela não existe\n";
    }

    echo "\n🛒 TABELA: empresas_marketplace\n";
    $exists = DB::select("SHOW TABLES LIKE 'empresas_marketplace'");
    if (!empty($exists)) {
        $columns = DB::select('DESCRIBE empresas_marketplace');
        foreach ($columns as $col) {
            echo "   - {$col->Field} ({$col->Type})\n";
        }
    } else {
        echo "   ❌ Tabela não existe\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 27) . "\n";
