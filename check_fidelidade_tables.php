<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $tables = DB::select("SHOW TABLES LIKE '%fidelidade%'");
    echo "Tabelas de fidelidade encontradas: " . count($tables) . "\n";

    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- " . $tableName . "\n";
    }

    // Também verificar tabelas relacionadas
    $relatedTables = DB::select("SHOW TABLES LIKE '%cashback%'");
    echo "\nTabelas de cashback encontradas: " . count($relatedTables) . "\n";

    foreach ($relatedTables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- " . $tableName . "\n";
    }

    $programaTables = DB::select("SHOW TABLES LIKE '%programa%'");
    echo "\nTabelas de programa encontradas: " . count($programaTables) . "\n";

    foreach ($programaTables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- " . $tableName . "\n";
    }
} catch (Exception $e) {
    echo "Erro ao verificar tabelas: " . $e->getMessage() . "\n";
}
