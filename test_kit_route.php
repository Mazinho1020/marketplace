<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

echo "<h2>Teste das Rotas de Kit</h2>";

// Verificar todas as rotas que contêm 'kit'
$routes = Route::getRoutes();
$kitRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'kit') !== false) {
        $kitRoutes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $uri,
            'name' => $route->getName(),
            'action' => $route->getActionName()
        ];
    }
}

echo "<h3>Rotas com 'kit' encontradas:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Método</th><th>URI</th><th>Nome</th><th>Action</th></tr>";

foreach ($kitRoutes as $route) {
    echo "<tr>";
    echo "<td>{$route['method']}</td>";
    echo "<td>{$route['uri']}</td>";
    echo "<td>{$route['name']}</td>";
    echo "<td>{$route['action']}</td>";
    echo "</tr>";
}

echo "</table>";

// Verificar especificamente a rota de buscar produto
echo "<h3>Verificação da rota buscar-produto:</h3>";
try {
    $buscarRoute = Route::getRoutes()->getByName('comerciantes.produtos.kits.buscar-produto');
    if ($buscarRoute) {
        echo "<p>✅ Rota 'comerciantes.produtos.kits.buscar-produto' encontrada!</p>";
        echo "<p>URI: " . $buscarRoute->uri() . "</p>";
        echo "<p>Métodos: " . implode(', ', $buscarRoute->methods()) . "</p>";
        echo "<p>Action: " . $buscarRoute->getActionName() . "</p>";

        // Tentar gerar a URL
        $url = route('comerciantes.produtos.kits.buscar-produto');
        echo "<p>URL gerada: $url</p>";
    } else {
        echo "<p>❌ Rota 'comerciantes.produtos.kits.buscar-produto' NÃO encontrada!</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao verificar rota: " . $e->getMessage() . "</p>";
}
