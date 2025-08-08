<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "📋 CAMPOS DA TABELA notificacao_enviadas:\n\n";

$campos = DB::select('DESCRIBE notificacao_enviadas');
foreach ($campos as $campo) {
    echo "- " . $campo->Field . " (" . $campo->Type . ")\n";
}

echo "\n🔍 VERIFICANDO NOTIFICAÇÃO DE EXEMPLO:\n\n";

$notificacao = DB::table('notificacao_enviadas')->first();
if ($notificacao) {
    echo "Propriedades do objeto:\n";
    foreach ((array)$notificacao as $key => $value) {
        echo "- $key: " . (is_string($value) ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
}
