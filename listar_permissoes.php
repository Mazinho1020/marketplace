<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LISTANDO TODAS AS PERMISSOES NO SISTEMA ===\n";

$permissoes = Illuminate\Support\Facades\DB::table('empresa_permissoes')->get();

echo "Total de permissões: " . $permissoes->count() . "\n\n";

foreach ($permissoes as $permissao) {
    echo "ID: {$permissao->id} | Nome: {$permissao->nome}\n";
    if (isset($permissao->descricao)) {
        echo "   Descrição: {$permissao->descricao}\n";
    }
    echo "---\n";
}
