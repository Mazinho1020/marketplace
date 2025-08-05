<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

echo "=== DIAGNÓSTICO DETALHADO DA SESSÃO ===\n\n";

try {
    // Simular inicialização da aplicação
    $request = Request::create('/comerciantes/horarios', 'GET');
    $kernel->handle($request);

    echo "1. 🔍 VERIFICANDO CONFIGURAÇÃO DE AUTENTICAÇÃO:\n";

    // Verificar providers
    $providers = config('auth.providers');
    echo "   Providers configurados:\n";
    foreach ($providers as $name => $config) {
        echo "   - $name: {$config['driver']} → {$config['model']}\n";
    }

    // Verificar guards
    $guards = config('auth.guards');
    echo "\n   Guards configurados:\n";
    foreach ($guards as $name => $config) {
        echo "   - $name: {$config['driver']} → provider '{$config['provider']}'\n";
    }

    echo "\n2. 🔐 TESTANDO AUTENTICAÇÃO MANUAL:\n";

    // Tentar fazer login manual
    $user = \App\Comerciantes\Models\EmpresaUsuario::find(3);
    if ($user) {
        echo "   ✅ Usuário encontrado: {$user->nome} ({$user->email})\n";

        // Verificar se o guard existe
        try {
            $guard = Auth::guard('comerciante');
            echo "   ✅ Guard 'comerciante' criado com sucesso\n";

            // Tentar login
            $guard->login($user);

            if ($guard->check()) {
                echo "   ✅ Login manual funcionou!\n";
                echo "   👤 Usuário logado: {$guard->user()->nome}\n";
                echo "   🆔 ID: {$guard->id()}\n";
            } else {
                echo "   ❌ Login manual falhou - guard->check() retornou false\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Erro ao criar guard: {$e->getMessage()}\n";
        }
    } else {
        echo "   ❌ Usuário ID 3 não encontrado\n";
    }

    echo "\n3. 🍪 VERIFICANDO CONFIGURAÇÃO DE SESSÃO:\n";

    $sessionConfig = config('session');
    echo "   Driver: {$sessionConfig['driver']}\n";
    echo "   Lifetime: {$sessionConfig['lifetime']} minutos\n";
    echo "   Cookie: {$sessionConfig['cookie']}\n";
    echo "   Domain: " . ($sessionConfig['domain'] ?? 'null') . "\n";
    echo "   Path: {$sessionConfig['path']}\n";
    echo "   Secure: " . ($sessionConfig['secure'] ? 'true' : 'false') . "\n";
    echo "   HTTP Only: " . ($sessionConfig['http_only'] ? 'true' : 'false') . "\n";

    echo "\n4. 🔧 VERIFICANDO MIDDLEWARE DE REDIRECIONAMENTO:\n";

    // Verificar onde o middleware auth redireciona quando falha
    echo "   Verificando configuração de redirecionamento...\n";

    // Para middleware auth:comerciante, verificar se há configuração específica
    if (config('auth.guards.comerciante')) {
        echo "   ✅ Guard 'comerciante' está configurado\n";

        // Verificar se há rota de login configurada
        try {
            $loginRoute = route('comerciantes.login');
            echo "   ✅ Rota de login: $loginRoute\n";
        } catch (Exception $e) {
            echo "   ❌ Erro ao resolver rota de login: {$e->getMessage()}\n";
        }
    } else {
        echo "   ❌ Guard 'comerciante' NÃO está configurado!\n";
    }

    echo "\n5. 🎯 CONCLUSÃO E PRÓXIMOS PASSOS:\n";
    echo "   Se tudo está configurado corretamente mas ainda redireciona,\n";
    echo "   o problema pode estar em:\n";
    echo "   - Configuração de cookies/domínio\n";
    echo "   - Middleware personalizado interceptando\n";
    echo "   - Problema na verificação de autenticação do Laravel\n";
    echo "   - Conflito de guards\n";
} catch (Exception $e) {
    echo "❌ ERRO GERAL: {$e->getMessage()}\n";
    echo "Arquivo: {$e->getFile()}\n";
    echo "Linha: {$e->getLine()}\n";
}
