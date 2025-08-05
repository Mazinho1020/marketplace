<?php
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Http\Request;

$app = \Illuminate\Foundation\Application::getInstance();

try {
    // Criar um request simples
    $request = Request::create('/comerciantes/horarios', 'GET');

    echo "=== TESTE DIRETO DE ROTA ===\n\n";

    // Verificar se a rota existe
    $router = $app->make('router');
    $routes = $router->getRoutes();

    echo "Verificando se a rota /comerciantes/horarios existe...\n";

    $routeFound = false;
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'horarios')) {
            echo "✅ Rota encontrada: " . $route->methods()[0] . " " . $route->uri() . "\n";
            $routeFound = true;
        }
    }

    if (!$routeFound) {
        echo "❌ Nenhuma rota de horários encontrada\n";
    }

    echo "\n=== RESULTADO ===\n";
    echo $routeFound ? "Sistema de rotas OK!" : "Problema nas rotas!";
    echo "\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
