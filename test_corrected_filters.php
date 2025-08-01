<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

try {
    echo "Testando filtros corrigidos...\n\n";

    // Simular a nova lógica do controller
    $groupFilter = 'sistema';
    $siteFilter = 'admin';
    $environmentFilter = 'producao';

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

    // Teste 1: Filtro de grupo
    echo "=== TESTE 1: Filtro de Grupo ===\n";
    $queryGroup = clone $query;
    $queryGroup->where('cg.codigo', $groupFilter);
    $resultsGroup = $queryGroup->get();
    echo "Resultados com filtro de grupo '$groupFilter': " . count($resultsGroup) . "\n\n";

    // Teste 2: Filtro de site
    echo "=== TESTE 2: Filtro de Site ===\n";
    $querySite = clone $query;
    $querySite->whereExists(function ($q) use ($siteFilter) {
        $q->select(DB::raw(1))
            ->from('config_sites as cs')
            ->whereColumn('cs.id', 'cv.site_id')
            ->where('cs.codigo', $siteFilter);
    });
    $resultsSite = $querySite->get();
    echo "Resultados com filtro de site '$siteFilter': " . count($resultsSite) . "\n\n";

    // Teste 3: Filtro de ambiente
    echo "=== TESTE 3: Filtro de Ambiente ===\n";
    $queryEnv = clone $query;
    $queryEnv->whereExists(function ($q) use ($environmentFilter) {
        $q->select(DB::raw(1))
            ->from('config_environments as ce')
            ->whereColumn('ce.id', 'cv.ambiente_id')
            ->where('ce.codigo', $environmentFilter);
    });
    $resultsEnv = $queryEnv->get();
    echo "Resultados com filtro de ambiente '$environmentFilter': " . count($resultsEnv) . "\n\n";

    // Teste 4: Filtros combinados
    echo "=== TESTE 4: Filtros Combinados ===\n";
    $queryCombined = clone $query;
    $queryCombined->where('cg.codigo', $groupFilter)
        ->whereExists(function ($q) use ($siteFilter) {
            $q->select(DB::raw(1))
                ->from('config_sites as cs')
                ->whereColumn('cs.id', 'cv.site_id')
                ->where('cs.codigo', $siteFilter);
        })
        ->whereExists(function ($q) use ($environmentFilter) {
            $q->select(DB::raw(1))
                ->from('config_environments as ce')
                ->whereColumn('ce.id', 'cv.ambiente_id')
                ->where('ce.codigo', $environmentFilter);
        });
    $resultsCombined = $queryCombined->get();
    echo "Resultados com todos os filtros: " . count($resultsCombined) . "\n";

    if (count($resultsCombined) > 0) {
        echo "Primeiro resultado:\n";
        $first = $resultsCombined->first();
        echo "- Chave: {$first->chave}\n";
        echo "- Grupo: {$first->grupo_nome}\n";
        echo "- Site ID: {$first->site_id}\n";
        echo "- Ambiente ID: {$first->ambiente_id}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
