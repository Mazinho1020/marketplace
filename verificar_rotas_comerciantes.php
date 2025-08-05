<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Route;

echo "=== VERIFICAÇÃO FINAL DAS ROTAS ===\n\n";

try {
    // Verificar se as rotas de comerciantes estão registradas
    $routes = Route::getRoutes();

    echo "1. 🔍 PROCURANDO ROTAS DE COMERCIANTES:\n";
    $comerciantesRoutes = [];

    foreach ($routes as $route) {
        $uri = $route->uri();
        if (str_contains($uri, 'comerciantes')) {
            $comerciantesRoutes[] = $uri;
        }
    }

    if (count($comerciantesRoutes) > 0) {
        echo "   ✅ Encontradas " . count($comerciantesRoutes) . " rotas de comerciantes:\n";
        foreach (array_slice($comerciantesRoutes, 0, 10) as $route) {
            echo "      - $route\n";
        }
        if (count($comerciantesRoutes) > 10) {
            echo "      ... e mais " . (count($comerciantesRoutes) - 10) . " rotas\n";
        }
    } else {
        echo "   ❌ PROBLEMA: Nenhuma rota de comerciantes encontrada!\n";
        echo "   📋 CAUSA: Arquivo routes/comerciante.php não está sendo carregado\n";
    }

    // Verificar rota específica de horários
    echo "\n2. 🎯 VERIFICANDO ROTA ESPECÍFICA:\n";

    $horariosRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'comerciantes/horarios') {
            $horariosRoute = $route;
            break;
        }
    }

    if ($horariosRoute) {
        echo "   ✅ Rota 'comerciantes/horarios' encontrada!\n";
        echo "   📋 Métodos: " . implode(', ', $horariosRoute->methods()) . "\n";
        echo "   🎮 Controller: " . $horariosRoute->getActionName() . "\n";
        echo "   🔐 Middleware: " . implode(', ', $horariosRoute->middleware()) . "\n";
    } else {
        echo "   ❌ PROBLEMA: Rota 'comerciantes/horarios' NÃO encontrada!\n";
    }

    echo "\n3. 📊 RESUMO:\n";
    echo "   - Total de rotas: " . $routes->count() . "\n";
    echo "   - Rotas de comerciantes: " . count($comerciantesRoutes) . "\n";

    if (count($comerciantesRoutes) > 0 && $horariosRoute) {
        echo "\n🎉 TUDO FUNCIONANDO! As rotas estão carregadas corretamente.\n";
        echo "🌐 Agora teste: http://localhost:8000/comerciantes/horarios\n";
    } else {
        echo "\n❌ PROBLEMA IDENTIFICADO: Rotas não estão carregadas.\n";
        echo "🔧 SOLUÇÃO: Verifique o arquivo bootstrap/app.php\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
