<?php
// DEPURAÇÃO DETALHADA DO REDIRECIONAMENTO

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

echo "=== DEPURAÇÃO DETALHADA DO REDIRECIONAMENTO ===\n\n";

try {
    // Criar um request
    $request = Request::create('/comerciantes/horarios', 'GET');

    // Inicializar a aplicação
    $kernel->handle($request);

    echo "1. 📋 VERIFICANDO GUARDS DISPONÍVEIS:\n";
    $guards = config('auth.guards');
    foreach ($guards as $name => $config) {
        echo "   - $name: " . $config['driver'] . " (" . $config['provider'] . ")\n";
    }

    echo "\n2. 🔍 VERIFICANDO ROTA ESPECÍFICA:\n";
    $router = app('router');
    $routes = $router->getRoutes();

    $targetRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'comerciantes/horarios' && in_array('GET', $route->methods())) {
            $targetRoute = $route;
            break;
        }
    }

    if ($targetRoute) {
        echo "   ✅ Rota encontrada: " . $targetRoute->uri() . "\n";
        echo "   📦 Middleware aplicado: " . implode(', ', $targetRoute->middleware()) . "\n";
        echo "   🎯 Ação: " . $targetRoute->getActionName() . "\n";

        // Verificar se há middleware customizado sendo aplicado
        echo "\n3. 🛡️ ANALISANDO MIDDLEWARES:\n";
        foreach ($targetRoute->middleware() as $middleware) {
            echo "   - $middleware\n";

            if ($middleware === 'auth:comerciante') {
                echo "     └─ ✅ Middleware de autenticação correto!\n";
            } elseif ($middleware === 'web') {
                echo "     └─ ✅ Middleware web padrão\n";
            } else {
                echo "     └─ ⚠️ Middleware customizado: $middleware\n";
            }
        }
    } else {
        echo "   ❌ Rota não encontrada!\n";
    }

    echo "\n4. 🔐 TESTANDO AUTENTICAÇÃO:\n";

    // Verificar se conseguimos fazer login manual
    $user = \App\Comerciantes\Models\EmpresaUsuario::find(3);
    if ($user) {
        Auth::guard('comerciante')->login($user);

        if (Auth::guard('comerciante')->check()) {
            echo "   ✅ Login manual funcionou! Usuário: " . Auth::guard('comerciante')->user()->nome . "\n";
            echo "   📧 Email: " . Auth::guard('comerciante')->user()->email . "\n";
        } else {
            echo "   ❌ Login manual falhou!\n";
        }
    } else {
        echo "   ❌ Usuário ID 3 não encontrado!\n";
    }

    echo "\n5. 🎯 TESTANDO MIDDLEWARE auth:comerciante:\n";

    // Simular o middleware auth:comerciante
    $authMiddleware = new \Illuminate\Auth\Middleware\Authenticate();

    try {
        echo "   Testando middleware de autenticação...\n";
        // Este seria o teste, mas pode ser complexo de simular
        echo "   ⚠️ Teste complexo - verificar manualmente\n";
    } catch (Exception $e) {
        echo "   ❌ Erro no middleware: " . $e->getMessage() . "\n";
    }

    echo "\n═══════════════════════════════════════════════════════════════\n";
    echo "💡 PRÓXIMOS PASSOS:\n";
    echo "1. Verificar se o usuário está realmente logado na sessão\n";
    echo "2. Testar fazer login manualmente primeiro\n";
    echo "3. Verificar se há redirecionamento no provider de autenticação\n";
    echo "═══════════════════════════════════════════════════════════════\n";
} catch (Exception $e) {
    echo "❌ ERRO GERAL: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}
