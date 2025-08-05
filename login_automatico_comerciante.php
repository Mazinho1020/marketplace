<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Comerciantes\Models\EmpresaUsuario;

echo "🔐 LOGIN AUTOMÁTICO COMERCIANTE\n";
echo "=" . str_repeat("=", 35) . "\n\n";

try {
    // Verificar se já está logado
    if (Auth::guard('comerciante')->check()) {
        echo "✅ Já está logado como: " . Auth::guard('comerciante')->user()->nome . "\n";
        echo "🎯 Pode acessar: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
        exit;
    }

    // Buscar usuário para login
    $user = EmpresaUsuario::where('email', 'mazinho@gmail.com')->first();

    if (!$user) {
        echo "❌ Usuário mazinho@gmail.com não encontrado\n";
        echo "📋 Usuários disponíveis:\n";
        $users = EmpresaUsuario::select('id', 'nome', 'email')->get();
        foreach ($users as $u) {
            echo "   - {$u->id}: {$u->nome} ({$u->email})\n";
        }
        exit;
    }

    echo "✅ Usuário encontrado: {$user->nome}\n";

    // Verificar senha
    $senhaCorreta = Hash::check('123456', $user->password);
    echo ($senhaCorreta ? "✅" : "❌") . " Senha correta: " . ($senhaCorreta ? "SIM" : "NÃO") . "\n";

    if (!$senhaCorreta) {
        echo "🔧 Atualizando senha para '123456'...\n";
        $user->password = Hash::make('123456');
        $user->save();
        echo "✅ Senha atualizada!\n";
    }

    // Fazer login programático
    Auth::guard('comerciante')->login($user);

    // Verificar se o login foi bem-sucedido
    if (Auth::guard('comerciante')->check()) {
        echo "✅ Login realizado com sucesso!\n";
        echo "👤 Logado como: " . Auth::guard('comerciante')->user()->nome . "\n";
        echo "\n🎯 PRÓXIMOS PASSOS:\n";
        echo "   1. Acesse: http://localhost:8000/comerciantes/dashboard\n";
        echo "   2. Ou acesse: http://localhost:8000/comerciantes/empresas\n";
        echo "   3. Ou acesse: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
    } else {
        echo "❌ Falha no login programático\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "   Linha: " . $e->getLine() . "\n";
}

echo "\n" . str_repeat("=", 37) . "\n";
