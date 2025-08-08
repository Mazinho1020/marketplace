<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== TESTE DE REDIRECIONAMENTO COMERCIANTES ===\n\n";

try {
    // 1. Testar acesso não autenticado a URL de comerciantes
    echo "1. 🔍 TESTANDO REDIRECIONAMENTO SEM LOGIN:\n";

    $request = Request::create('/comerciantes/empresas/1/usuarios', 'GET');
    $request->setLaravelSession(app('session.store'));

    try {
        $response = $kernel->handle($request);

        if ($response->getStatusCode() === 302) {
            $location = $response->headers->get('Location');
            echo "   ✅ Status: 302 (redirecionamento)\n";
            echo "   📍 Para: " . $location . "\n";

            if (str_contains($location, 'comerciantes/login')) {
                echo "   ✅ CORRETO: Redirecionando para login de comerciantes!\n";
            } elseif (str_contains($location, '/login')) {
                echo "   ❌ PROBLEMA: Redirecionando para login de admin!\n";
            } else {
                echo "   ⚠️ OUTRO: Redirecionamento inesperado\n";
            }
        } else {
            echo "   ❌ Status: " . $response->getStatusCode() . " (esperado 302)\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Erro: " . $e->getMessage() . "\n";
    }

    echo "\n2. 🧪 TESTE COM DIFERENTES URLS:\n";

    $urlsParaTestar = [
        '/comerciantes/dashboard',
        '/comerciantes/empresas',
        '/comerciantes/empresas/1/usuarios',
        '/comerciantes/horarios'
    ];

    foreach ($urlsParaTestar as $url) {
        echo "   Testando: $url\n";
        $testRequest = Request::create($url, 'GET');
        $testRequest->setLaravelSession(app('session.store'));

        try {
            $testResponse = $kernel->handle($testRequest);

            if ($testResponse->getStatusCode() === 302) {
                $location = $testResponse->headers->get('Location');
                if (str_contains($location, 'comerciantes/login')) {
                    echo "     ✅ Correto\n";
                } else {
                    echo "     ❌ Problema: " . $location . "\n";
                }
            } else {
                echo "     ⚠️ Status: " . $testResponse->getStatusCode() . "\n";
            }
        } catch (Exception $e) {
            echo "     ❌ Erro: " . $e->getMessage() . "\n";
        }
    }

    echo "\n3. 📊 RESULTADO FINAL:\n";
    echo "   Se todos os testes mostraram '✅ Correto', o problema foi resolvido!\n";
    echo "   Se há '❌ Problema', ainda precisamos investigar mais.\n\n";

    echo "🔗 PRÓXIMOS PASSOS:\n";
    echo "   1. Acesse: http://localhost:8000/comerciantes/login\n";
    echo "   2. Faça login com suas credenciais\n";
    echo "   3. Depois tente: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
} catch (Exception $e) {
    echo "❌ Erro crítico: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
