<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MAPEAMENTO DE COLUNAS ===\n\n";

// Mapping das colunas que precisam ser ajustadas
$mapeamento = [
    'Nosso modelo' => 'Tabela existente',
    'valor_bruto' => 'valor',
    'intervalo_dias' => 'intervalo_parcelas',
    'config_juros_multa' => 'juros_multa_config',
    'config_desconto' => 'desconto_antecipacao',
];

echo "Colunas que precisam ser mapeadas:\n";
foreach ($mapeamento as $nosso => $existente) {
    echo "- $nosso -> $existente\n";
}

// Verificar se as colunas mapeadas existem
echo "\nVerificando existência das colunas mapeadas:\n";
$colunas = DB::select('SHOW COLUMNS FROM lancamentos');
$colunasExistentes = array_column($colunas, 'Field');

foreach ($mapeamento as $nosso => $existente) {
    $existe = in_array($existente, $colunasExistentes);
    echo "- $existente: " . ($existe ? 'EXISTE' : 'NÃO EXISTE') . "\n";
}
?>
