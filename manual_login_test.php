<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\Auth;

echo "=== TESTE DE LOGIN MANUAL ===\n";

try {
    // Buscar usuário de teste
    $user = EmpresaUsuario::where('email', 'admin@teste.com')->first();

    if ($user) {
        echo "✅ Usuário encontrado: {$user->nome}\n";

        // Fazer login manual no guard comerciante
        Auth::guard('comerciante')->login($user);

        if (Auth::guard('comerciante')->check()) {
            echo "✅ Login realizado com sucesso!\n";
            echo "   Usuário logado: " . Auth::guard('comerciante')->user()->nome . "\n";
        } else {
            echo "❌ Falha no login\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n🔗 AGORA TENTE ACESSAR:\n";
echo "- Dashboard: http://localhost:8000/comerciantes/dashboard\n";
echo "- Empresas: http://localhost:8000/comerciantes/empresas\n";
echo "\n✅ TESTE CONCLUÍDO!\n";
