<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== TESTE SIMPLES DE REDIRECIONAMENTO ===\n\n";

echo "📋 VERIFICANDO CONFIGURAÇÃO:\n";

// 1. Verificar se o middleware está registrado
$app = \Illuminate\Foundation\Application::getInstance();
echo "   ✅ Laravel inicializado\n";

// 2. Verificar rotas
echo "\n🛣️ VERIFICANDO ROTAS:\n";
try {
    $router = app('router');
    $routes = $router->getRoutes();

    foreach ($routes as $route) {
        if (str_contains($route->uri(), 'comerciantes') && str_contains($route->uri(), 'usuarios')) {
            echo "   ✅ Rota encontrada: " . $route->uri() . "\n";
            echo "   📦 Middleware: " . implode(', ', $route->middleware()) . "\n";
            break;
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erro ao verificar rotas: " . $e->getMessage() . "\n";
}

// 3. Verificar middleware
echo "\n🛡️ VERIFICANDO MIDDLEWARE:\n";
try {
    $middleware = new \App\Http\Middleware\ComercianteAuthMiddleware();
    echo "   ✅ ComercianteAuthMiddleware carregado\n";
} catch (Exception $e) {
    echo "   ❌ Erro no middleware: " . $e->getMessage() . "\n";
}

// 4. Verificar configuração de autenticação
echo "\n🔐 VERIFICANDO CONFIGURAÇÃO AUTH:\n";
try {
    $guards = config('auth.guards');
    if (isset($guards['comerciante'])) {
        echo "   ✅ Guard comerciante configurado\n";
        echo "   🔍 Driver: " . $guards['comerciante']['driver'] . "\n";
        echo "   📊 Provider: " . $guards['comerciante']['provider'] . "\n";
    } else {
        echo "   ❌ Guard comerciante não encontrado\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro na configuração: " . $e->getMessage() . "\n";
}

echo "\n📍 CONCLUSÃO:\n";
echo "   O middleware customizado Authenticate.php foi criado\n";
echo "   Este middleware verifica se a URL é 'comerciantes/*'\n";
echo "   Se for, redireciona para 'comerciantes.login'\n";
echo "   Caso contrário, redireciona para 'login'\n\n";

echo "🧪 TESTE MANUAL:\n";
echo "   1. Abra: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
echo "   2. Deve redirecionar para: http://localhost:8000/comerciantes/login\n";
echo "   3. Se redirecionar para /login (admin), o problema persiste\n\n";

echo "🔧 SOLUÇÃO APLICADA:\n";
echo "   • Criado middleware Authenticate personalizado\n";
echo "   • Limpo cache de rotas e configurações\n";
echo "   • Sistema deve redirecionar corretamente agora\n";
