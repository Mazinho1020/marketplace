<?php

require_once 'vendor/autoload.php';

use App\Http\Controllers\Financial\ContasPagarController;
use App\Models\Empresa;
use Illuminate\Http\Request;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $empresa = Empresa::first();
    if (!$empresa) {
        echo "Nenhuma empresa encontrada\n";
        exit;
    }

    echo "Testando ContasPagarController...\n";
    echo "Empresa: {$empresa->nome}\n";

    $controller = new ContasPagarController();
    $request = new Request();

    // Testando o método index
    $result = $controller->index($request, $empresa->id);
    echo "Index executado com sucesso!\n";
    echo "Tipo do resultado: " . get_class($result) . "\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
