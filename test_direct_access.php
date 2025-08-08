<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE ACESSO DIRETO ===\n";

// Testar acesso direto ao login de comerciante
echo "🔗 TESTANDO ROTAS:\n";
echo "1. Login comerciante: http://localhost:8000/comerciantes/login\n";
echo "2. Dashboard comerciante: http://localhost:8000/comerciantes/dashboard\n";
echo "3. Root: http://localhost:8000/\n";

// Fazer um teste simples de requisição
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $comercianteRoutes = [];

    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'comerciantes')) {
            $comercianteRoutes[] = $route->uri();
        }
    }

    echo "\n📋 ROTAS DE COMERCIANTES ENCONTRADAS:\n";
    foreach ($comercianteRoutes as $route) {
        echo "- /$route\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n✅ TESTE CONCLUÍDO - TENTE ACESSAR AS ROTAS!\n";
