<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "CAMPOS RELACIONADOS A PARCELAS:\n";
    $result = DB::select('DESCRIBE lancamentos');
    foreach ($result as $col) {
        if (strpos($col->Field, 'parcel') !== false) {
            echo "  - $col->Field ($col->Type)\n";
        }
    }
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

?>
