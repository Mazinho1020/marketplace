<?php
require_once __DIR__ . '/vendor/autoload.php';

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== TESTE DE AUTENTICAÇÃO E ROTA ===\n\n";

try {
    // 1. Verificar se existe usuário comerciante
    echo "1. Verificando usuários comerciantes...\n";

    $usuarios = DB::table('empresa_usuarios')->get();
    echo "   Encontrados " . $usuarios->count() . " usuários\n";

    if ($usuarios->count() == 0) {
        echo "   ❌ Nenhum usuário encontrado - criando usuário teste...\n";

        // Criar usuário teste
        $userId = DB::table('empresa_usuarios')->insertGetId([
            'nome' => 'Teste Usuario',
            'email' => 'teste@exemplo.com',
            'password' => Hash::make('123456'),
            'empresa_id' => 1,
            'ativo' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        echo "   ✅ Usuário criado com ID: $userId\n";
    } else {
        echo "   ✅ Usuários existem\n";
        foreach ($usuarios as $user) {
            echo "      - ID: {$user->id}, Email: {$user->email}, Empresa: {$user->empresa_id}\n";
        }
    }

    // 2. Testar rota diretamente
    echo "\n2. Testando configuração de rotas...\n";

    // Verificar se a rota existe
    $router = app('router');
    $routes = $router->getRoutes();

    $horariosRoutes = [];
    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'horarios')) {
            $horariosRoutes[] = $route->uri();
        }
    }

    if (count($horariosRoutes) > 0) {
        echo "   ✅ Rotas de horários encontradas:\n";
        foreach ($horariosRoutes as $routeUri) {
            echo "      - $routeUri\n";
        }
    } else {
        echo "   ❌ Nenhuma rota de horários encontrada\n";
    }

    // 3. Verificar middleware
    echo "\n3. Verificando middleware aplicado...\n";

    $route = $routes->getByName('comerciantes.horarios.index');
    if ($route) {
        echo "   ✅ Rota 'comerciantes.horarios.index' encontrada\n";
        echo "   URI: " . $route->uri() . "\n";
        echo "   Middleware: " . implode(', ', $route->middleware()) . "\n";
    } else {
        echo "   ❌ Rota 'comerciantes.horarios.index' não encontrada\n";
    }

    echo "\n=== RESULTADO ===\n";
    echo "Para resolver o problema de redirecionamento:\n";
    echo "1. Verifique se há middleware forçando seleção de empresa\n";
    echo "2. Teste fazer login primeiro em /comerciantes/login\n";
    echo "3. Depois acesse /comerciantes/horarios\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
}
