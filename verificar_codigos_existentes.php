<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICANDO CÓDIGOS EXISTENTES NO BANCO ===\n\n";

// Verificar todos os códigos existentes
$codigos = App\Models\ProdutoCodigoBarras::all();

foreach ($codigos as $codigo) {
    echo "ID: {$codigo->id} | Código: {$codigo->codigo} | Tipo: {$codigo->tipo}\n";

    $teste = new App\Models\ProdutoCodigoBarras([
        'tipo' => $codigo->tipo,
        'codigo' => $codigo->codigo
    ]);

    $valido = $teste->isValido();
    echo "Válido segundo a validação: " . ($valido ? 'Sim' : 'Não') . "\n";

    if (!$valido && $codigo->tipo === 'ean13') {
        // Calcular código correto
        $codigoLimpo = preg_replace('/[^0-9]/', '', $codigo->codigo);
        if (strlen($codigoLimpo) === 13) {
            $soma = 0;
            for ($i = 0; $i < 12; $i++) {
                $multiplicador = ($i % 2 === 0) ? 1 : 3;
                $soma += intval($codigoLimpo[$i]) * $multiplicador;
            }
            $digitoCorreto = (10 - ($soma % 10)) % 10;
            $codigoCorreto = substr($codigoLimpo, 0, 12) . $digitoCorreto;
            echo "Código correto seria: {$codigoCorreto}\n";
        }
    }
    echo "---\n";
}

echo "\n=== SOLUÇÃO RECOMENDADA ===\n";
echo "1. Flexibilizar validação para códigos internos\n";
echo "2. Ou corrigir códigos existentes\n";
echo "3. Ou desabilitar validação rigorosa temporariamente\n";
