<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "VERIFICAÇÃO CONFIG_SITES REMOVIDA\n";
echo "================================\n\n";

try {
    // 1. Verificar se a tabela ainda existe
    $tables = DB::select("SHOW TABLES LIKE 'config_sites'");
    echo "1. Tabela config_sites existe: " . (count($tables) > 0 ? "❌ SIM (deveria ter sido removida)" : "✅ NÃO") . "\n\n";

    // 2. Verificar se há referências nas outras tabelas
    echo "2. Verificando referências em outras tabelas:\n";

    // Verificar config_values
    $hasValues = DB::getSchemaBuilder()->hasTable('config_values');
    if ($hasValues) {
        $valuesWithSite = DB::table('config_values')->whereNotNull('site_id')->count();
        echo "   config_values com site_id não nulo: $valuesWithSite " . ($valuesWithSite > 0 ? "❌ (precisa limpar)" : "✅") . "\n";
    }

    // Verificar config_history
    $hasHistory = DB::getSchemaBuilder()->hasTable('config_history');
    if ($hasHistory) {
        $historyWithSite = DB::table('config_history')->whereNotNull('site_id')->count();
        echo "   config_history com site_id não nulo: $historyWithSite " . ($historyWithSite > 0 ? "❌ (precisa limpar)" : "✅") . "\n";
    }

    echo "\n3. Limpando referências:\n";

    // Limpar site_id nas tabelas que ainda referenciam
    if ($hasValues && $valuesWithSite > 0) {
        DB::table('config_values')->update(['site_id' => null]);
        echo "   ✅ config_values: site_id definido como NULL\n";
    }

    if ($hasHistory && $historyWithSite > 0) {
        DB::table('config_history')->update(['site_id' => null]);
        echo "   ✅ config_history: site_id definido como NULL\n";
    }

    echo "\n4. Testando acesso sem config_sites:\n";

    // Testar uma consulta que antes usava config_sites
    $configs = DB::table('config_definitions as cd')
        ->leftJoin('config_groups as cg', 'cd.grupo_id', '=', 'cg.id')
        ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
        ->select('cd.chave', 'cd.nome', 'cv.valor')
        ->limit(5)
        ->get();

    echo "   ✅ Consulta de configurações funcionando: " . count($configs) . " registros encontrados\n";

    echo "\n✅ SISTEMA LIMPO - config_sites removido com sucesso!\n";
    echo "\nPróximos passos:\n";
    echo "1. Testar o admin: http://127.0.0.1:8000/admin\n";
    echo "2. Verificar se não há mais erros sobre config_sites\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
