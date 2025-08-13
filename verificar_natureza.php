<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\ContaGerencial;

echo "Verificando valores de natureza na tabela:\n";

try {
    $naturezas = ContaGerencial::distinct('natureza')->pluck('natureza');
    echo "Valores encontrados: " . json_encode($naturezas->toArray()) . "\n";

    // Verificar alguns registros
    $contas = ContaGerencial::select('id', 'nome', 'natureza')->limit(5)->get();
    echo "\nPrimeiros 5 registros:\n";
    foreach ($contas as $conta) {
        echo "ID: {$conta->id}, Nome: {$conta->nome}, Natureza: {$conta->natureza->value} ({$conta->natureza->label()})\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
