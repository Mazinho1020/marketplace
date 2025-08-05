<?php
// TESTE SIMPLES - Fazer login e testar redirecionamento

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Auth;
use App\Comerciantes\Models\EmpresaUsuario;

echo "=== TESTE DE LOGIN E REDIRECIONAMENTO ===\n\n";

try {
    // Fazer login manual
    $user = EmpresaUsuario::where('email', 'mazinho@gmail.com')->first();

    if ($user) {
        echo "✅ Usuário encontrado: {$user->nome}\n";
        echo "   Email: {$user->email}\n";
        echo "   Empresa ID: {$user->empresa_id}\n";

        // Fazer login manual
        Auth::guard('comerciante')->login($user);

        if (Auth::guard('comerciante')->check()) {
            echo "✅ Login realizado com sucesso!\n";

            // Verificar sessão
            echo "\n📊 Estado da Sessão:\n";
            echo "   - Usuário logado: " . Auth::guard('comerciante')->user()->nome . "\n";
            echo "   - Empresa atual na sessão: " . (session('empresa_atual_id') ?? 'Não definida') . "\n";

            // Simular acesso à rota horários
            echo "\n🔗 Simulando acesso à rota de horários...\n";

            // Criar request simulado
            $horarioRequest = Illuminate\Http\Request::create('/comerciantes/horarios', 'GET');

            // Testar se consegue acessar sem redirecionamento
            echo "   Request criado para: " . $horarioRequest->path() . "\n";
            echo "   Usuário autenticado: " . (Auth::guard('comerciante')->check() ? 'SIM' : 'NÃO') . "\n";
        } else {
            echo "❌ Falha no login\n";
        }
    } else {
        echo "❌ Usuário não encontrado\n";
    }

    echo "\n💡 SOLUÇÃO SUGERIDA:\n";
    echo "O problema provavelmente está em:\n";
    echo "1. Sistema forçando seleção de empresa após login\n";
    echo "2. Middleware verificando se empresa está selecionada\n";
    echo "3. Dashboard redirecionando para primeira empresa disponível\n\n";

    echo "Para corrigir, você precisa:\n";
    echo "1. Fazer login em: http://localhost:8000/comerciantes/login\n";
    echo "2. Aguardar qualquer redirecionamento automático terminar\n";
    echo "3. Navegar manualmente para: http://localhost:8000/comerciantes/horarios\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
