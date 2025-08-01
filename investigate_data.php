<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Investigando dados de sites e ambientes...\n\n";

    // Verificar quais sites estão sendo usados nos config_values
    echo "Sites usados em config_values:\n";
    $sitesUsed = DB::table('config_values as cv')
        ->join('config_sites as cs', 'cv.site_id', '=', 'cs.id')
        ->select('cs.codigo', 'cs.nome', DB::raw('COUNT(*) as total'))
        ->groupBy('cs.codigo', 'cs.nome')
        ->get();

    foreach ($sitesUsed as $site) {
        echo "- {$site->codigo} ({$site->nome}): {$site->total} configurações\n";
    }
    echo "\n";

    // Verificar quais ambientes estão sendo usados nos config_values
    echo "Ambientes usados em config_values:\n";
    $environmentsUsed = DB::table('config_values as cv')
        ->join('config_environments as ce', 'cv.ambiente_id', '=', 'ce.id')
        ->select('ce.codigo', 'ce.nome', DB::raw('COUNT(*) as total'))
        ->groupBy('ce.codigo', 'ce.nome')
        ->get();

    foreach ($environmentsUsed as $env) {
        echo "- {$env->codigo} ({$env->nome}): {$env->total} configurações\n";
    }
    echo "\n";

    // Verificar alguns registros de config_values
    echo "Primeiros 10 registros de config_values:\n";
    $values = DB::table('config_values as cv')
        ->join('config_sites as cs', 'cv.site_id', '=', 'cs.id')
        ->join('config_environments as ce', 'cv.ambiente_id', '=', 'ce.id')
        ->join('config_definitions as cd', 'cv.config_id', '=', 'cd.id')
        ->select('cd.chave', 'cs.codigo as site_codigo', 'ce.codigo as ambiente_codigo', 'cv.valor')
        ->limit(10)
        ->get();

    foreach ($values as $value) {
        echo "- {$value->chave} | Site: {$value->site_codigo} | Ambiente: {$value->ambiente_codigo} | Valor: {$value->valor}\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
