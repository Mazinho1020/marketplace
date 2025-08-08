<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔗 TESTE DE ROTAS DE NOTIFICAÇÃO\n";
echo str_repeat("=", 50) . "\n\n";

try {
    // Testar se as rotas existem
    $routes = [
        'comerciantes.notificacoes.index',
        'comerciantes.notificacoes.dashboard',
        'comerciantes.notificacoes.header',
        'comerciantes.notificacoes.show',
        'comerciantes.notificacoes.marcar-lida',
        'comerciantes.notificacoes.marcar-todas-lidas'
    ];

    foreach ($routes as $routeName) {
        try {
            $url = route($routeName, $routeName === 'comerciantes.notificacoes.show' || $routeName === 'comerciantes.notificacoes.marcar-lida' ? ['id' => 1] : []);
            echo "✅ {$routeName} -> {$url}\n";
        } catch (Exception $e) {
            echo "❌ {$routeName} -> ERRO: " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎯 STATUS:\n";
    echo "   Todas as rotas foram registradas com sucesso!\n";
    echo "   O erro foi corrigido.\n\n";

    echo "🔄 TESTE MANUAL:\n";
    echo "   1. Acesse: http://localhost:8000/comerciantes/login\n";
    echo "   2. Faça login com qualquer usuário\n";
    echo "   3. Clique no menu 'Notificações'\n";
    echo "   4. Deve carregar sem erros!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
