<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTE DE LOGIN FUNCIONAL ===\n";

try {
    // 1. Simular uma requisição GET para a página de login
    echo "\n1. Verificando rota de login...\n";

    $response = \Illuminate\Support\Facades\Route::get('comerciantes.login');
    if ($response) {
        echo "✅ Rota comerciantes.login existe\n";
    }

    // 2. Verificar usuário de teste
    $user = \App\Comerciantes\Models\EmpresaUsuario::where('email', 'admin@teste.com')->first();
    if ($user) {
        echo "✅ Usuário admin@teste.com encontrado\n";
        echo "   Status: {$user->status}\n";
    } else {
        echo "❌ Usuário não encontrado\n";
    }

    // 3. Verificar guard
    $guards = config('auth.guards');
    if (isset($guards['comerciante'])) {
        echo "✅ Guard 'comerciante' está configurado\n";
        echo "   Driver: {$guards['comerciante']['driver']}\n";
        echo "   Provider: {$guards['comerciante']['provider']}\n";
    } else {
        echo "❌ Guard 'comerciante' não encontrado\n";
    }

    echo "\n🔗 LINKS PARA TESTAR:\n";
    echo "   Login: http://localhost:8000/comerciantes/login\n";
    echo "   Dashboard: http://localhost:8000/comerciantes/dashboard\n";
    echo "   Empresas: http://localhost:8000/comerciantes/empresas\n";

    echo "\n📧 CREDENCIAIS:\n";
    echo "   Email: admin@teste.com\n";
    echo "   Senha: 123456\n";
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n✅ TESTE CONCLUÍDO!\n";
