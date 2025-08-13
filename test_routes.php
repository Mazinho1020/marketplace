<?php

// Teste de rotas de produtos relacionados
echo "Testing routes...\n";

try {
    echo "1. Testando route comerciantes.produtos.index: ";
    $route1 = route('comerciantes.produtos.index');
    echo "✅ $route1\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

try {
    echo "2. Testando route comerciantes.produtos.relacionados.index com produto ID 1: ";
    $route2 = route('comerciantes.produtos.relacionados.index', 1);
    echo "✅ $route2\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

try {
    echo "3. Listando todas as rotas que contêm 'relacionados':\n";
    $routes = collect(Route::getRoutes())->filter(function ($route) {
        return str_contains($route->getName() ?? '', 'relacionados');
    });

    foreach ($routes as $route) {
        echo "   - " . $route->getName() . " -> " . $route->uri() . "\n";
    }

    if ($routes->isEmpty()) {
        echo "   ❌ Nenhuma rota de 'relacionados' encontrada!\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
