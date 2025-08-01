<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Testando filtros da página de configuração...\n\n";

    // Simular request com filtros
    $groupFilter = 'sistema'; // Código do grupo
    $siteFilter = 'admin';     // Código do site
    $environmentFilter = 'producao'; // Código do ambiente

    echo "Testando com filtros:\n";
    echo "- Grupo: $groupFilter\n";
    echo "- Site: $siteFilter\n";
    echo "- Ambiente: $environmentFilter\n\n";

    // Query base
    $query = DB::table('config_definitions as cd')
        ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
        ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
        ->select([
            'cd.*',
            'cg.nome as grupo_nome',
            'cg.codigo as grupo_codigo',
            'cg.icone_class as grupo_icone',
            'cv.valor',
            'cv.site_id',
            'cv.ambiente_id'
        ]);

    echo "Verificando se o filtro de grupo funciona...\n";
    // PROBLEMA IDENTIFICADO: O controller está filtrando por cd.grupo_id mas deveria filtrar por cg.codigo
    $queryWithGroupFilter = clone $query;
    $queryWithGroupFilter->where('cg.codigo', $groupFilter);

    $resultsWithGroup = $queryWithGroupFilter->get();
    echo "Resultados com filtro de grupo: " . count($resultsWithGroup) . "\n";

    if (count($resultsWithGroup) > 0) {
        echo "Primeiro resultado com filtro de grupo:\n";
        $first = $resultsWithGroup->first();
        echo "- Chave: {$first->chave}\n";
        echo "- Grupo código: {$first->grupo_codigo}\n";
        echo "- Grupo nome: {$first->grupo_nome}\n\n";
    }

    echo "Verificando sites disponíveis...\n";
    $sites = DB::table('config_sites')->select('id', 'codigo', 'nome')->get();
    foreach ($sites as $site) {
        echo "- Site ID: {$site->id}, Código: {$site->codigo}, Nome: {$site->nome}\n";
    }
    echo "\n";

    echo "Verificando ambientes disponíveis...\n";
    $environments = DB::table('config_environments')->select('id', 'codigo', 'nome')->get();
    foreach ($environments as $env) {
        echo "- Ambiente ID: {$env->id}, Código: {$env->codigo}, Nome: {$env->nome}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
