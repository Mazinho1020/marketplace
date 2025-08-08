<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Verificando todas as colunas da tabela produtos:\n";
    $todasColunas = DB::select("SHOW COLUMNS FROM produtos");
    foreach ($todasColunas as $coluna) {
        echo "- " . $coluna->Field . " (" . $coluna->Type . ")\n";
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
