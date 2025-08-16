<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== ESTRUTURA DA TABELA LANCAMENTO_ITENS ===\n";
    $result = DB::select('DESCRIBE lancamento_itens');
    foreach ($result as $col) {
        echo "  - $col->Field ($col->Type)\n";
    }
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

?>
