<?php

/**
 * Script de debug avançado para diagnosticar o problema HTTP 302
 */

require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';

echo "<h1>🔍 DEBUG AVANÇADO - HTTP 302</h1>";

try {
    echo "<h2>1. Verificação de Autenticação</h2>";

    // Verificar guard comerciante
    $isAuth = \Illuminate\Support\Facades\Auth::guard('comerciante')->check();
    echo "<p>Usuário autenticado: " . ($isAuth ? "✅ SIM" : "❌ NÃO") . "</p>";

    if ($isAuth) {
        $user = \Illuminate\Support\Facades\Auth::guard('comerciante')->user();
        echo "<p>Usuário: " . $user->nome . " (" . $user->email . ")</p>";
        echo "<p>Empresa ID: " . $user->empresa_id . "</p>";
    }

    echo "<h2>2. Teste de Middleware</h2>";

    // Simular request
    $request = \Illuminate\Http\Request::create('/comerciantes/empresas/1/horarios', 'GET');
    $request->setLaravelSession(app('session.store'));

    // Fazer login programático para teste
    $testUser = \App\Comerciantes\Models\EmpresaUsuario::first();
    if ($testUser) {
        \Illuminate\Support\Facades\Auth::guard('comerciante')->login($testUser);
        echo "<p>Login de teste realizado: ✅</p>";
        echo "<p>Guard check após login: " . (\Illuminate\Support\Facades\Auth::guard('comerciante')->check() ? "✅" : "❌") . "</p>";
    }

    echo "<h2>3. Teste de Rotas</h2>";

    // Listar rotas relacionadas
    $routes = \Illuminate\Support\Facades\Route::getRoutes()->getRoutesByName();
    $horarioRoutes = array_filter($routes, function ($route, $name) {
        return strpos($name, 'horarios') !== false;
    }, ARRAY_FILTER_USE_BOTH);

    echo "<p>Rotas de horários encontradas: " . count($horarioRoutes) . "</p>";
    foreach ($horarioRoutes as $name => $route) {
        echo "<li><code>$name</code> → " . $route->uri() . "</li>";
    }

    echo "<h2>4. Teste Direto do Controller</h2>";

    try {
        $controller = new \App\Comerciantes\Controllers\HorarioController();

        // Testar método index
        ob_start();
        $result = $controller->index(1);
        $output = ob_get_clean();

        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            echo "<p>❌ Controller retornou REDIRECT</p>";
            echo "<p>Redirecionando para: " . $result->getTargetUrl() . "</p>";
            echo "<p>Status: " . $result->getStatusCode() . "</p>";
        } elseif ($result instanceof \Illuminate\View\View) {
            echo "<p>✅ Controller retornou VIEW</p>";
            echo "<p>View: " . $result->getName() . "</p>";
        } else {
            echo "<p>⚠️ Resposta inesperada: " . get_class($result) . "</p>";
        }
    } catch (\Exception $e) {
        echo "<p>❌ Erro no controller: " . $e->getMessage() . "</p>";
        echo "<p>Linha: " . $e->getLine() . " em " . basename($e->getFile()) . "</p>";
    }

    echo "<h2>5. Debug da Rota Específica</h2>";

    try {
        // Tentar resolver a rota
        $route = \Illuminate\Support\Facades\Route::getRoutes()->match($request);
        echo "<p>✅ Rota encontrada: " . $route->getName() . "</p>";
        echo "<p>Controller: " . $route->getActionName() . "</p>";
        echo "<p>Middleware: " . implode(', ', $route->middleware()) . "</p>";

        // Testar middleware manualmente
        $middleware = new \App\Http\Middleware\ComercianteAuthMiddleware();
        $response = $middleware->handle($request, function ($req) {
            return response('✅ Middleware passou!');
        });

        echo "<p>Teste de middleware: Status " . $response->getStatusCode() . "</p>";
        echo "<p>Conteúdo: " . $response->getContent() . "</p>";
    } catch (\Exception $e) {
        echo "<p>❌ Erro na rota: " . $e->getMessage() . "</p>";
    }
} catch (\Exception $e) {
    echo "<h2>❌ ERRO CRÍTICO</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>🔗 Links de Teste</h2>";
echo "<p><a href='/comerciantes/login' target='_blank'>1. Fazer Login</a></p>";
echo "<p><a href='/comerciantes/dashboard' target='_blank'>2. Dashboard</a></p>";
echo "<p><a href='/comerciantes/empresas/1/horarios' target='_blank'>3. Testar Horários (PROBLEMA)</a></p>";
echo "<p><a href='/teste-horarios-debug/1' target='_blank'>4. Rota de Debug</a></p>";
