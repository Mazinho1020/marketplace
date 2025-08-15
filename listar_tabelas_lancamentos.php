<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;

echo "🔍 Procurando tabelas de lançamentos...\n";
echo "======================================\n\n";

try {
    $tables = DB::select('SHOW TABLES');

    echo "📋 Tabelas que contêm 'lancamento':\n";

    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        if (strpos($tableName, 'lancamento') !== false) {
            echo "   - $tableName\n";
        }
    }

    echo "\n📋 Todas as tabelas do banco:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array) $table)[0];
        echo "   - $tableName\n";
    }
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
