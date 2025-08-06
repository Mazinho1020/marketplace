<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Carregar a aplicação Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "=== DEBUG ROTAS HORÁRIOS ===\n\n";

// Criar request simulado
$request = Request::create('/comerciantes/empresas/1/horarios', 'GET');
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Headers:\n";
foreach ($response->headers->all() as $name => $values) {
    foreach ($values as $value) {
        echo "  $name: $value\n";
    }
}

if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
    echo "\nRedirect detectado!\n";
    echo "Location: " . $response->headers->get('Location') . "\n";
}

echo "\nContent:\n";
echo substr($response->getContent(), 0, 500) . "...\n";

echo "\n=== FIM DEBUG ===\n";
