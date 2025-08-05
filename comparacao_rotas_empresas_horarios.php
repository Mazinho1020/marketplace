<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

echo "=== COMPARAÇÃO: EMPRESAS vs HORÁRIOS ===\n\n";

try {
    // Inicializar aplicação
    $request = Request::create('/', 'GET');
    $kernel->handle($request);

    // Rotas para comparar
    $routes = [
        'comerciantes/empresas/1/edit' => 'FUNCIONA',
        'comerciantes/horarios' => 'NÃO FUNCIONA'
    ];

    $router = app('router');
    $allRoutes = $router->getRoutes();

    foreach ($routes as $uri => $status) {
        echo "🔍 ANALISANDO: $uri ($status)\n";
        echo "═══════════════════════════════════════════════════════════════\n";

        // Encontrar a rota
        $targetRoute = null;
        foreach ($allRoutes as $route) {
            if ($route->uri() === $uri || $route->uri() === str_replace('/1', '/{empresa}', $uri)) {
                $targetRoute = $route;
                break;
            }
        }

        if ($targetRoute) {
            echo "✅ ROTA ENCONTRADA:\n";
            echo "   URI: {$targetRoute->uri()}\n";
            echo "   Métodos: " . implode(', ', $targetRoute->methods()) . "\n";
            echo "   Controller: {$targetRoute->getActionName()}\n";
            echo "   Nome: " . ($targetRoute->getName() ?? 'Sem nome') . "\n";
            echo "   Middleware: " . implode(', ', $targetRoute->middleware()) . "\n";

            // Verificar diferenças nos middlewares
            $middlewares = $targetRoute->middleware();
            echo "\n   🛡️ ANÁLISE DOS MIDDLEWARES:\n";
            foreach ($middlewares as $middleware) {
                echo "      - $middleware";

                if ($middleware === 'web') {
                    echo " (padrão do Laravel)\n";
                } elseif ($middleware === 'auth:comerciante') {
                    echo " (autenticação Laravel nativa)\n";
                } elseif ($middleware === 'auth.comerciante') {
                    echo " (nosso middleware personalizado)\n";
                } else {
                    echo " (outro middleware)\n";
                }
            }

            // Verificar controller
            $controllerClass = explode('@', $targetRoute->getActionName())[0];
            $method = explode('@', $targetRoute->getActionName())[1] ?? 'index';

            echo "\n   🎯 CONTROLLER:\n";
            echo "      Classe: $controllerClass\n";
            echo "      Método: $method\n";

            if (class_exists($controllerClass)) {
                echo "      Status: ✅ Existe\n";

                if (method_exists($controllerClass, $method)) {
                    echo "      Método: ✅ Existe\n";
                } else {
                    echo "      Método: ❌ NÃO existe\n";
                }
            } else {
                echo "      Status: ❌ NÃO existe\n";
            }
        } else {
            echo "❌ ROTA NÃO ENCONTRADA!\n";
        }

        echo "\n";
    }

    echo "🔍 INVESTIGAÇÃO ESPECÍFICA:\n";
    echo "═══════════════════════════════════════════════════════════════\n";

    // Verificar se há diferença no middleware aplicado
    echo "1. 🛡️ COMPARANDO MIDDLEWARES:\n";

    $empresaRoute = null;
    $horarioRoute = null;

    foreach ($allRoutes as $route) {
        if (strpos($route->uri(), 'comerciantes/empresas/{empresa}/edit') !== false) {
            $empresaRoute = $route;
        }
        if ($route->uri() === 'comerciantes/horarios') {
            $horarioRoute = $route;
        }
    }

    if ($empresaRoute && $horarioRoute) {
        echo "   Empresa Middleware: " . implode(', ', $empresaRoute->middleware()) . "\n";
        echo "   Horário Middleware: " . implode(', ', $horarioRoute->middleware()) . "\n";

        $empresaMid = $empresaRoute->middleware();
        $horarioMid = $horarioRoute->middleware();

        if ($empresaMid === $horarioMid) {
            echo "   ✅ Middlewares IDÊNTICOS\n";
        } else {
            echo "   ⚠️ Middlewares DIFERENTES:\n";
            echo "      Empresa tem: " . implode(', ', array_diff($empresaMid, $horarioMid)) . "\n";
            echo "      Horário tem: " . implode(', ', array_diff($horarioMid, $empresaMid)) . "\n";
        }
    }

    echo "\n2. 🎯 CONCLUSÃO:\n";
    echo "   Se os middlewares são diferentes, isso explica o problema!\n";
    echo "   A rota de empresas provavelmente usa middleware diferente\n";
    echo "   que não está causando o redirecionamento.\n";

    echo "\n3. 💡 PRÓXIMO PASSO:\n";
    echo "   Verificar qual middleware a rota de empresas está usando\n";
    echo "   e aplicar o mesmo para a rota de horários.\n";
} catch (Exception $e) {
    echo "❌ ERRO: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
}
