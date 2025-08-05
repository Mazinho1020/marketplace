<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = \Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "=== TESTE PÓS-CORREÇÃO ===\n\n";

echo "🔧 CONFIGURAÇÃO CORRIGIDA:\n";
echo "   APP_URL no .env: " . env('APP_URL') . "\n";
echo "   config('app.url'): " . config('app.url') . "\n";

try {
    echo "\n🔗 TESTE DE ROTA:\n";
    $loginRoute = route('comerciantes.login');
    echo "   Rota de login: $loginRoute\n";

    if (strpos($loginRoute, ':8000') !== false) {
        echo "   ✅ URL corrigida com sucesso! Agora inclui :8000\n";
    } else {
        echo "   ❌ URL ainda não está correta\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erro ao resolver rota: " . $e->getMessage() . "\n";
}

echo "\n🎯 RESULTADO:\n";
echo "Agora tente acessar novamente:\n";
echo "1. Fazer logout se ainda estiver logado\n";
echo "2. Fazer login em: http://localhost:8000/comerciantes/login\n";
echo "3. Acessar: http://localhost:8000/comerciantes/horarios\n";
echo "\nO redirecionamento deve funcionar corretamente agora!\n";
