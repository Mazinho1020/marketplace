<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Estrutura da tabela fidelidade_carteiras:\n";
$columns = DB::select('SHOW COLUMNS FROM fidelidade_carteiras');
foreach ($columns as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\nDados existentes:\n";
$count = DB::table('fidelidade_carteiras')->count();
echo "Total de registros: {$count}\n";

if ($count > 0) {
    $sample = DB::table('fidelidade_carteiras')->limit(3)->get();
    foreach ($sample as $row) {
        echo "- ID: {$row->id}, Cliente ID: {$row->cliente_id}\n";
    }
}
