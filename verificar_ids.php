<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "IDs dos códigos de barras:\n";
$codigos = App\Models\ProdutoCodigoBarras::all(['id', 'codigo']);
foreach ($codigos as $codigo) {
    echo "ID: {$codigo->id} - Código: {$codigo->codigo}\n";
}
