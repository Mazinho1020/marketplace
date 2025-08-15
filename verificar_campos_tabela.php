<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$columns = DB::select('DESCRIBE lancamentos');
echo 'Campos da tabela lancamentos:' . PHP_EOL;
foreach ($columns as $column) {
    echo '  ' . $column->Field . ' (' . $column->Type . ')' . PHP_EOL;
}
