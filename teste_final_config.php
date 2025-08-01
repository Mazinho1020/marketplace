<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "TESTE FINAL - CONFIG_SITES REMOVIDO\n";
echo "===================================\n\n";

try {
    // Testar se conseguimos buscar configurações sem erro
    echo "1. Testando consulta de configurações...\n";
    $configs = DB::table('config_definitions')->count();
    echo "   ✅ Total de definições de config: $configs\n";

    $values = DB::table('config_values')->count();
    echo "   ✅ Total de valores de config: $values\n";

    echo "\n2. Testando valores específicos sem site_id...\n";
    $valuesWithoutSite = DB::table('config_values')->whereNull('site_id')->count();
    echo "   ✅ Valores sem site_id: $valuesWithoutSite\n";

    $valuesWithSite = DB::table('config_values')->whereNotNull('site_id')->count();
    echo "   " . ($valuesWithSite > 0 ? "⚠️" : "✅") . " Valores com site_id: $valuesWithSite\n";

    echo "\n3. Testando consulta complexa (como fazia antes)...\n";
    $resultado = DB::table('config_definitions as cd')
        ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
        ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
        ->select('cd.chave', 'cd.nome', 'cg.nome as grupo', 'cv.valor')
        ->limit(3)
        ->get();

    echo "   ✅ Consulta executada com sucesso! Exemplos:\n";
    foreach ($resultado as $config) {
        echo "      - {$config->chave} ({$config->grupo}): " . ($config->valor ?? 'sem valor') . "\n";
    }

    echo "\n🎉 SUCESSO! Sistema funcionando sem config_sites!\n";
    echo "\nPode acessar: http://127.0.0.1:8000/admin\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
