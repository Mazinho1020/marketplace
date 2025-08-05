<?php
// Teste que simula exatamente o comportamento do navegador

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Http\Request;

echo "=== SIMULAÇÃO EXATA DO NAVEGADOR ===\n\n";

try {
    // Simular request HTTP exatamente como navegador
    $request = Request::create(
        'http://localhost:8000/comerciantes/horarios',
        'GET',
        [], // parameters
        [], // cookies
        [], // files
        [   // server variables
            'HTTP_HOST' => 'localhost:8000',
            'REQUEST_URI' => '/comerciantes/horarios',
            'REQUEST_METHOD' => 'GET',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => '8000',
            'HTTPS' => false,
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    );

    echo "1. 🌐 REQUEST CRIADO:\n";
    echo "   URL: " . $request->fullUrl() . "\n";
    echo "   Method: " . $request->method() . "\n";
    echo "   Path: " . $request->path() . "\n";

    echo "\n2. 🔄 PROCESSANDO REQUEST:\n";

    // Processar através do kernel como faria o servidor web
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request);

    echo "   Status Code: " . $response->getStatusCode() . "\n";
    echo "   Status Text: " . $response->statusText() . "\n";

    if ($response->isRedirect()) {
        echo "   🔄 REDIRECIONAMENTO DETECTADO!\n";
        echo "   Para: " . $response->getTargetUrl() . "\n";

        // Verificar headers de redirecionamento
        echo "   Headers:\n";
        foreach ($response->headers->all() as $key => $values) {
            if (strtolower($key) === 'location') {
                echo "      Location: " . implode(', ', $values) . "\n";
            }
        }
    } else {
        echo "   ✅ RESPOSTA NORMAL (sem redirecionamento)\n";
        echo "   Content length: " . strlen($response->getContent()) . " bytes\n";

        // Mostrar início do conteúdo
        $content = $response->getContent();
        if (strlen($content) > 100) {
            echo "   Preview: " . substr($content, 0, 100) . "...\n";
        } else {
            echo "   Content: $content\n";
        }
    }

    echo "\n3. 🔍 VERIFICANDO POSSÍVEIS CAUSAS:\n";

    if ($response->getStatusCode() === 302) {
        $location = $response->headers->get('Location');
        echo "   🎯 Redirecionando para: $location\n";

        if (strpos($location, 'login') !== false) {
            echo "   💡 CAUSA: Middleware de autenticação (usuário não logado)\n";
        } elseif (strpos($location, 'empresas') !== false) {
            echo "   💡 CAUSA: Redirecionamento para seleção de empresa\n";
        } else {
            echo "   💡 CAUSA: Outro tipo de redirecionamento\n";
        }

        echo "\n   📋 SOLUÇÃO:\n";
        echo "   1. Fazer login primeiro em: http://localhost:8000/comerciantes/login\n";
        echo "   2. Depois acessar: http://localhost:8000/comerciantes/horarios\n";
    }

    echo "\n4. 🛠️ TESTE ADICIONAL - VERIFICAR ROTA:\n";

    // Verificar se a rota está registrada corretamente
    $router = $app['router'];
    $routes = $router->getRoutes();

    $found = false;
    foreach ($routes as $route) {
        if ($route->uri() === 'comerciantes/horarios') {
            $found = true;
            echo "   ✅ Rota registrada: " . $route->uri() . "\n";
            echo "   🎯 Action: " . $route->getActionName() . "\n";
            echo "   🛡️ Middleware: " . implode(', ', $route->middleware()) . "\n";
            break;
        }
    }

    if (!$found) {
        echo "   ❌ Rota NÃO encontrada!\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
