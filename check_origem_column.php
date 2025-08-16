<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $result = DB::select('SHOW COLUMNS FROM lancamentos WHERE Field = "origem"');
    if (!empty($result)) {
        $col = $result[0];
        echo 'Campo origem: ' . $col->Type . ' | Null: ' . $col->Null . ' | Default: ' . $col->Default . "\n";
        
        // Verificar se é ENUM
        if (strpos($col->Type, 'enum') !== false) {
            echo "É um ENUM! Valores possíveis: " . $col->Type . "\n";
        }
    } else {
        echo "Campo origem não encontrado!\n";
    }
    
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}

?>
