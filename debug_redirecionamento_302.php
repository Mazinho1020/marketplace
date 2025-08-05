<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Models\EmpresaUsuario;

echo "=== TESTE DE REDIRECIONAMENTO 302 ===\n\n";

try {
    // Fazer login primeiro
    $user = EmpresaUsuario::where('email', 'mazinho@gmail.com')->first();

    if ($user) {
        Auth::guard('comerciante')->login($user);
        echo "✅ Login realizado como: {$user->nome}\n";
        echo "   Autenticado: " . (Auth::guard('comerciante')->check() ? 'SIM' : 'NÃO') . "\n\n";

        // Simular request para a rota problemática
        $request = Request::create('/comerciantes/horarios', 'GET');
        $request->setLaravelSession(app('session.store'));

        echo "🔍 SIMULANDO REQUEST:\n";
        echo "   URL: /comerciantes/horarios\n";
        echo "   Método: GET\n";
        echo "   Sessão ativa: " . ($request->hasSession() ? 'SIM' : 'NÃO') . "\n\n";

        // Processar o request
        echo "📡 PROCESSANDO REQUEST...\n";
        $response = $kernel->handle($request);

        echo "   Status Code: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() === 302) {
            $location = $response->headers->get('Location');
            echo "   ❌ REDIRECIONAMENTO DETECTADO!\n";
            echo "   Redirecionando para: " . $location . "\n\n";

            // Analisar o redirecionamento
            if (str_contains($location, 'login')) {
                echo "🔍 ANÁLISE: Redirecionamento para LOGIN\n";
                echo "   • Problema: Middleware de autenticação\n";
                echo "   • Solução: Verificar guard comerciante\n";
            } elseif (str_contains($location, 'empresas')) {
                echo "🔍 ANÁLISE: Redirecionamento para EMPRESAS\n";
                echo "   • Problema: Sistema forçando seleção de empresa\n";
                echo "   • Solução: Verificar middleware de empresa\n";
            } elseif (str_contains($location, 'dashboard')) {
                echo "🔍 ANÁLISE: Redirecionamento para DASHBOARD\n";
                echo "   • Problema: Lógica no controller\n";
                echo "   • Solução: Verificar método index\n";
            } else {
                echo "🔍 ANÁLISE: Redirecionamento DESCONHECIDO\n";
                echo "   • Investigar: " . $location . "\n";
            }
        } else {
            echo "   ✅ STATUS OK: " . $response->getStatusCode() . "\n";
            echo "   Conteúdo: " . substr($response->getContent(), 0, 200) . "...\n";
        }
    } else {
        echo "❌ Usuário não encontrado para teste\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "   Arquivo: " . $e->getFile() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}
