<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ConfigController;

try {
    echo "Teste final dos filtros corrigidos...\n\n";

    // Simular requests com filtros
    $tests = [
        ['group' => 'sistema'],
        ['site' => 'marketplace_web'],
        ['environment' => 'producao'],
        ['search' => 'app'],
        ['group' => 'sistema', 'environment' => 'producao']
    ];

    foreach ($tests as $index => $filters) {
        echo "=== TESTE " . ($index + 1) . ": " . json_encode($filters) . " ===\n";

        // Simular request
        $request = new Request($filters);

        // Recriar a lógica do controller
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
            ]);

        // Aplicar filtros
        if ($request->filled('search')) {
            $searchFilter = $request->get('search');
            $query->where(function ($q) use ($searchFilter) {
                $q->where('cd.nome', 'like', "%{$searchFilter}%")
                    ->orWhere('cd.chave', 'like', "%{$searchFilter}%")
                    ->orWhere('cd.descricao', 'like', "%{$searchFilter}%");
            });
        }

        if ($request->filled('group')) {
            $groupFilter = $request->get('group');
            $query->where('cg.codigo', $groupFilter);
        }

        if ($request->filled('site')) {
            $siteFilter = $request->get('site');
            $query->whereExists(function ($q) use ($siteFilter) {
                $q->select(DB::raw(1))
                    ->from('config_sites as cs')
                    ->whereColumn('cs.id', 'cv.site_id')
                    ->where('cs.codigo', $siteFilter);
            });
        }

        if ($request->filled('environment')) {
            $environmentFilter = $request->get('environment');
            $query->whereExists(function ($q) use ($environmentFilter) {
                $q->select(DB::raw(1))
                    ->from('config_environments as ce')
                    ->whereColumn('ce.id', 'cv.ambiente_id')
                    ->where('ce.codigo', $environmentFilter);
            });
        }

        $results = $query->orderBy('cg.ordem', 'asc')
            ->orderBy('cd.ordem', 'asc')
            ->get();

        echo "Resultados encontrados: " . count($results) . "\n";

        if (count($results) > 0) {
            echo "Primeiros resultados:\n";
            foreach ($results->take(3) as $result) {
                echo "- {$result->chave} ({$result->grupo_nome})\n";
            }
        }
        echo "\n";
    }

    echo "Todos os testes concluídos!\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
