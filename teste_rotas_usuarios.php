<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "🧪 TESTE DE ROTAS - USUÁRIOS EMPRESAS\n";
echo "=" . str_repeat("=", 45) . "\n\n";

try {
    // Testar se a rota existe
    $routeExists = Route::has('comerciantes.empresas.usuarios.index');
    echo ($routeExists ? "✅" : "❌") . " Rota comerciantes.empresas.usuarios.index: " . ($routeExists ? "Existe" : "Não existe") . "\n";

    if ($routeExists) {
        $route = Route::getRoutes()->getByName('comerciantes.empresas.usuarios.index');
        echo "   URI: " . $route->uri() . "\n";
        echo "   Métodos: " . implode(', ', $route->methods()) . "\n";
    }

    // Testar outras rotas relacionadas
    $routes = [
        'comerciantes.empresas.usuarios.store',
        'comerciantes.empresas.usuarios.update',
        'comerciantes.empresas.usuarios.destroy'
    ];

    foreach ($routes as $routeName) {
        $exists = Route::has($routeName);
        echo ($exists ? "✅" : "❌") . " Rota $routeName: " . ($exists ? "Existe" : "Não existe") . "\n";
    }

    echo "\n🎯 TESTE DA URL:\n";
    echo "   Acesse: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 47) . "\n";
