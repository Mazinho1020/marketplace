<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testando rotas de fidelidade...\n\n";

$routes = [
    'admin.fidelidade.dashboard',
    'admin.fidelidade.index',
    'admin.fidelidade.clientes',
    'admin.fidelidade.configuracoes'
];

foreach ($routes as $route) {
    try {
        $url = route($route);
        echo "✓ {$route} -> {$url}\n";
    } catch (Exception $e) {
        echo "✗ {$route} -> ERRO: {$e->getMessage()}\n";
    }
}

echo "\nTeste concluído!\n";
