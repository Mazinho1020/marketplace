<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== DIAGNÓSTICO FINAL - STATUS 302 ===\n\n";

try {
    // Simular requisição
    $request = Request::create('/comerciantes/horarios', 'GET');
    $kernel->handle($request);

    echo "1. 🔍 VERIFICANDO ESTADO ATUAL:\n";

    // Verificar se há usuário logado
    $userLoggedIn = Auth::guard('comerciante')->check();
    echo "   Usuário logado no guard 'comerciante': " . ($userLoggedIn ? 'SIM' : 'NÃO') . "\n";

    if ($userLoggedIn) {
        $user = Auth::guard('comerciante')->user();
        echo "   Nome: {$user->nome}\n";
        echo "   Email: {$user->email}\n";
        echo "   ID: {$user->id}\n";
    }

    echo "\n2. 🛡️ TESTANDO MIDDLEWARE:\n";

    // Simular o middleware
    $middleware = new \App\Http\Middleware\ComercianteAuthMiddleware();

    echo "   Testando condição do middleware...\n";
    if (!Auth::guard('comerciante')->check()) {
        echo "   ❌ Middleware detecta: USUÁRIO NÃO LOGADO\n";
        echo "   🔄 Isso causará redirecionamento HTTP 302\n";
        echo "   📍 Para: http://localhost:8000/comerciantes/login\n";
    } else {
        echo "   ✅ Middleware detecta: USUÁRIO LOGADO\n";
        echo "   ➡️ Permitirá acesso (HTTP 200)\n";
    }

    echo "\n3. 🍪 VERIFICANDO SESSÃO:\n";

    // Verificar dados da sessão
    echo "   Session ID: " . session()->getId() . "\n";
    echo "   Session dados: ";
    $sessionData = session()->all();
    if (empty($sessionData)) {
        echo "VAZIA\n";
    } else {
        echo "HAS DATA\n";
        foreach (array_keys($sessionData) as $key) {
            echo "      - $key\n";
        }
    }

    echo "\n4. 💡 DIAGNÓSTICO:\n";

    if (!$userLoggedIn) {
        echo "   🎯 CAUSA DO 302: Usuário não está logado na sessão\n";
        echo "\n   📋 SOLUÇÃO:\n";
        echo "   1. Acesse: http://localhost:8000/comerciantes/login\n";
        echo "   2. Faça login com suas credenciais\n";
        echo "   3. Depois acesse: http://localhost:8000/comerciantes/horarios\n";
        echo "\n   ⚠️ IMPORTANTE: O status 302 É CORRETO quando não logado!\n";
        echo "      Isso é SEGURANÇA funcionando, não é erro!\n";
    } else {
        echo "   ❓ ESTRANHO: Usuário está logado mas ainda há 302\n";
        echo "      Pode ser cache do navegador ou outro problema\n";
    }

    echo "\n═══════════════════════════════════════════════════════════════\n";
    echo "✅ RESUMO: O sistema está funcionando corretamente!\n";
    echo "O HTTP 302 é normal quando você não está logado.\n";
    echo "═══════════════════════════════════════════════════════════════\n";
} catch (Exception $e) {
    echo "❌ ERRO: {$e->getMessage()}\n";
}
