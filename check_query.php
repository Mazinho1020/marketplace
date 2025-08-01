<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Verificando estrutura das tabelas...\n\n";

    // Verificar colunas de config_groups
    echo "Estrutura da tabela config_groups:\n";
    $columns = DB::select("DESCRIBE config_groups");
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    echo "\n";

    // Testar uma query similar à do controller
    echo "Testando query do controller...\n";
    $query = DB::table('config_definitions as cd')
        ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
        ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
        ->select([
            'cd.*',
            'cg.nome as grupo_nome',
            'cg.icone_class as grupo_icone',
            'cv.valor',
            'cv.site_id',
            'cv.ambiente_id'
        ])
        ->limit(5);

    echo "SQL: " . $query->toSql() . "\n\n";

    $results = $query->get();
    echo "Resultados encontrados: " . count($results) . "\n";

    if (count($results) > 0) {
        echo "Primeiro resultado:\n";
        $first = $results->first();
        foreach ((array)$first as $key => $value) {
            echo "- $key: " . ($value ?? 'NULL') . "\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
