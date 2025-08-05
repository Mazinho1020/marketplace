<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== VERIFICAÇÃO DETALHADA - USUÁRIO LOGADO ===\n\n";

try {
    // Simular inicialização completa como o navegador faria
    $request = Request::create('/comerciantes/horarios', 'GET', [], [], [], [
        'HTTP_HOST' => 'localhost:8000',
        'HTTP_USER_AGENT' => 'Mozilla/5.0 Test',
        'HTTP_ACCEPT' => 'text/html,application/xhtml+xml',
        'REQUEST_URI' => '/comerciantes/horarios',
        'REQUEST_METHOD' => 'GET',
        'SERVER_NAME' => 'localhost',
        'SERVER_PORT' => '8000'
    ]);

    // Inicializar sessão
    $response = $kernel->handle($request);

    echo "1. 🔍 VERIFICANDO ESTADO DE AUTENTICAÇÃO:\n";

    // Verificar todos os guards
    $guards = ['web', 'comerciante'];
    foreach ($guards as $guardName) {
        $isAuthenticated = Auth::guard($guardName)->check();
        echo "   Guard '$guardName': " . ($isAuthenticated ? 'LOGADO' : 'NÃO LOGADO') . "\n";

        if ($isAuthenticated) {
            $user = Auth::guard($guardName)->user();
            echo "      └─ Usuário: {$user->nome} (ID: {$user->id})\n";
        }
    }

    echo "\n2. 🍪 DADOS DA SESSÃO:\n";
    $sessionData = session()->all();
    foreach ($sessionData as $key => $value) {
        if (strpos($key, 'login') !== false || strpos($key, 'auth') !== false) {
            echo "   $key: " . (is_string($value) ? $value : gettype($value)) . "\n";
        }
    }

    echo "\n3. 🛡️ TESTANDO MIDDLEWARE MANUALMENTE:\n";

    // Fazer login manual para teste
    $user = \App\Comerciantes\Models\EmpresaUsuario::find(3);
    if ($user) {
        Auth::guard('comerciante')->login($user);
        echo "   ✅ Login manual realizado\n";
        echo "   ✅ Guard check: " . (Auth::guard('comerciante')->check() ? 'SIM' : 'NÃO') . "\n";

        // Testar o middleware
        $middleware = new \App\Http\Middleware\ComercianteAuthMiddleware();

        try {
            $result = $middleware->handle($request, function ($req) {
                return response('SUCESSO - ACESSO PERMITIDO');
            });

            echo "   ✅ Middleware result: " . $result->getContent() . "\n";
        } catch (Exception $e) {
            echo "   ❌ Middleware error: " . $e->getMessage() . "\n";
        }
    }

    echo "\n4. 🔗 TESTANDO ROTA ESPECÍFICA:\n";

    // Verificar se a rota existe e está acessível
    $router = app('router');
    $routes = $router->getRoutes();

    $targetRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'comerciantes/horarios') {
            $targetRoute = $route;
            break;
        }
    }

    if ($targetRoute) {
        echo "   ✅ Rota encontrada: {$targetRoute->uri()}\n";
        echo "   🎯 Controller: {$targetRoute->getActionName()}\n";
        echo "   🛡️ Middleware: " . implode(', ', $targetRoute->middleware()) . "\n";

        // Verificar se o controller existe
        $controllerClass = explode('@', $targetRoute->getActionName())[0];
        if (class_exists($controllerClass)) {
            echo "   ✅ Controller class exists: $controllerClass\n";

            // Verificar se o método existe
            $method = explode('@', $targetRoute->getActionName())[1] ?? 'index';
            if (method_exists($controllerClass, $method)) {
                echo "   ✅ Method exists: $method\n";
            } else {
                echo "   ❌ Method NOT found: $method\n";
            }
        } else {
            echo "   ❌ Controller class NOT found: $controllerClass\n";
        }
    } else {
        echo "   ❌ Rota 'comerciantes/horarios' NÃO encontrada!\n";
    }

    echo "\n5. 📄 VERIFICANDO VIEW:\n";

    // Verificar se a view existe
    $viewPath = 'comerciantes.horarios.index';
    try {
        $viewExists = view()->exists($viewPath);
        echo "   View '$viewPath': " . ($viewExists ? 'EXISTS' : 'NOT FOUND') . "\n";

        if (!$viewExists) {
            echo "   📁 Verificando caminho físico...\n";
            $physicalPath = resource_path('views/comerciantes/horarios/index.blade.php');
            echo "   Path: $physicalPath\n";
            echo "   File exists: " . (file_exists($physicalPath) ? 'YES' : 'NO') . "\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Error checking view: " . $e->getMessage() . "\n";
    }

    echo "\n═══════════════════════════════════════════════════════════════\n";
    echo "💡 PRÓXIMOS PASSOS BASEADOS NO RESULTADO ACIMA\n";
    echo "═══════════════════════════════════════════════════════════════\n";
} catch (Exception $e) {
    echo "❌ ERRO GERAL: {$e->getMessage()}\n";
    echo "File: {$e->getFile()}\n";
    echo "Line: {$e->getLine()}\n";
}
