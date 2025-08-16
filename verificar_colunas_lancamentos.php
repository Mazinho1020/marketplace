<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$colunas = DB::select('SHOW COLUMNS FROM lancamentos');
echo "Colunas da tabela lancamentos:\n";
foreach ($colunas as $coluna) {
    echo "- {$coluna->Field}\n";
}

// Verificar especificamente colunas relacionadas a parcelas
echo "\nColunas relacionadas a parcelas:\n";
$parcelasColunas = ['intervalo_dias', 'intervalo_parcelas', 'parcela_atual', 'total_parcelas', 'grupo_parcelas'];
foreach ($parcelasColunas as $coluna) {
    $existe = array_search($coluna, array_column($colunas, 'Field')) !== false;
    echo "- $coluna: " . ($existe ? 'EXISTE' : 'NÃO EXISTE') . "\n";
}
?>
