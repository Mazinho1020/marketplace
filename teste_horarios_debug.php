<?php

/**
 * Script de debug para testar acesso direto às rotas de horários
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Testar autenticação
echo "<h1>DEBUG - Teste de Acesso aos Horários</h1>";

try {
    // Simular requisição
    $request = \Illuminate\Http\Request::create('/comerciantes/empresas/1/horarios', 'GET');

    echo "<h2>1. Testando Guard Comerciante</h2>";

    // Verificar se existe usuário comerciante
    $users = \App\Comerciantes\Models\EmpresaUsuario::all();
    echo "<p>Usuários comerciantes encontrados: " . $users->count() . "</p>";

    if ($users->count() > 0) {
        $user = $users->first();
        echo "<p>Primeiro usuário: " . $user->email . "</p>";

        // Fazer login programático
        \Illuminate\Support\Facades\Auth::guard('comerciante')->login($user);

        echo "<p>Login realizado: " . (\Illuminate\Support\Facades\Auth::guard('comerciante')->check() ? 'SIM' : 'NÃO') . "</p>";
        echo "<p>User ID logado: " . (\Illuminate\Support\Facades\Auth::guard('comerciante')->id() ?? 'NENHUM') . "</p>";
    }

    echo "<h2>2. Testando Middleware</h2>";

    // Testar middleware manualmente
    $middleware = new \App\Http\Middleware\ComercianteAuthMiddleware();
    $response = $middleware->handle($request, function ($req) {
        return response('Middleware passou!');
    });

    echo "<p>Status da resposta do middleware: " . $response->getStatusCode() . "</p>";
    echo "<p>Conteúdo: " . $response->getContent() . "</p>";

    echo "<h2>3. Testando Controller Diretamente</h2>";

    // Testar controller diretamente
    $controller = new \App\Comerciantes\Controllers\HorarioController();

    try {
        $result = $controller->index(1);
        echo "<p>Controller executou com sucesso!</p>";
        echo "<p>Tipo de resposta: " . get_class($result) . "</p>";
    } catch (\Exception $e) {
        echo "<p>Erro no controller: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></p>";
    }
} catch (\Exception $e) {
    echo "<p><strong>ERRO GERAL:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Arquivo: " . $e->getFile() . " Linha: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>4. Links de Teste</h2>";
echo "<p><a href='/comerciantes/login'>Ir para Login</a></p>";
echo "<p><a href='/comerciantes/empresas/1/horarios'>Tentar Horários (pode redirecionar)</a></p>";
echo "<p><a href='/teste-horarios-debug/1'>Rota de debug</a></p>";
