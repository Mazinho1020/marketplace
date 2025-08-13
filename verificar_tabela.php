<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Estrutura da tabela produto_codigos_barras:\n";
    $columns = DB::select('DESCRIBE produto_codigos_barras');
    foreach ($columns as $col) {
        echo "- {$col->Field} ({$col->Type})" . ($col->Null == 'YES' ? ' NULL' : ' NOT NULL') . "\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
