<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testando acesso ao campo 'ativo':\n";
$clientes = DB::table('funforcli as fc')
    ->join('empresas as e', 'fc.empresa_id', '=', 'e.id')
    ->where('fc.tipo', 'cliente')
    ->select('fc.id', 'fc.nome', 'fc.ativo', 'fc.pontos_acumulados')
    ->limit(3)
    ->get();

foreach ($clientes as $cliente) {
    echo "Cliente: {$cliente->nome}, Ativo: {$cliente->ativo}, Pontos: {$cliente->pontos_acumulados}\n";
}
echo "\n✓ Teste concluído sem erros - campo 'ativo' acessível!\n";
