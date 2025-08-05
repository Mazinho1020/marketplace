<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "🔐 VERIFICAÇÃO DE AUTENTICAÇÃO COMERCIANTE\n";
echo "=" . str_repeat("=", 45) . "\n\n";

try {
    // Verificar se está logado no guard comerciante
    $isLoggedCommerce = Auth::guard('comerciante')->check();
    echo ($isLoggedCommerce ? "✅" : "❌") . " Logado como comerciante: " . ($isLoggedCommerce ? "SIM" : "NÃO") . "\n";

    if ($isLoggedCommerce) {
        $user = Auth::guard('comerciante')->user();
        echo "   Usuário: {$user->nome}\n";
        echo "   Email: {$user->email}\n";
        echo "   ID: {$user->id}\n";
    }

    // Verificar se está logado no guard padrão
    $isLoggedDefault = Auth::check();
    echo ($isLoggedDefault ? "✅" : "❌") . " Logado no guard padrão: " . ($isLoggedDefault ? "SIM" : "NÃO") . "\n";

    if ($isLoggedDefault) {
        $user = Auth::user();
        echo "   Usuário padrão: " . ($user->name ?? $user->nome ?? 'Nome não disponível') . "\n";
    }

    echo "\n🔍 INFORMAÇÕES DE SESSÃO:\n";

    // Verificar sessão
    if (session()->has('_token')) {
        echo "   ✅ Sessão ativa\n";
    } else {
        echo "   ❌ Sessão não encontrada\n";
    }

    // Verificar se o guard comerciante está configurado
    $guards = config('auth.guards');
    echo "\n🛡️ GUARDS CONFIGURADOS:\n";
    foreach ($guards as $name => $config) {
        echo "   - {$name}: driver={$config['driver']}, provider={$config['provider']}\n";
    }

    echo "\n🎯 PRÓXIMOS PASSOS:\n";
    if (!$isLoggedCommerce) {
        echo "   1. Acesse: http://localhost:8000/comerciantes/login\n";
        echo "   2. Use: mazinho@gmail.com / 123456\n";
        echo "   3. Depois tente: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
    } else {
        echo "   ✅ Você está logado! O problema pode ser outro.\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 47) . "\n";
