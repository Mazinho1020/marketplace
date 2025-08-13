<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE DE VALIDAÇÃO DE CÓDIGO DE BARRAS ===\n\n";

// Código que está dando erro
$codigo = '7891234567899';
echo "Testando código: {$codigo}\n";

// Criar instância do modelo para testar
$codigoBarras = new App\Models\ProdutoCodigoBarras([
    'tipo' => 'ean13',
    'codigo' => $codigo
]);

echo "Tipo: {$codigoBarras->tipo}\n";
echo "É válido? " . ($codigoBarras->isValido() ? 'Sim' : 'Não') . "\n\n";

// Vamos calcular manualmente o dígito verificador correto
echo "Calculando dígito verificador para EAN-13:\n";
$codigoLimpo = preg_replace('/[^0-9]/', '', $codigo);
echo "Código limpo: {$codigoLimpo}\n";
echo "Tamanho: " . strlen($codigoLimpo) . "\n";

if (strlen($codigoLimpo) === 13) {
    $soma = 0;
    echo "Cálculo:\n";
    for ($i = 0; $i < 12; $i++) {
        $multiplicador = ($i % 2 === 0) ? 1 : 3;
        $valor = intval($codigoLimpo[$i]) * $multiplicador;
        $soma += $valor;
        echo "Posição {$i}: {$codigoLimpo[$i]} x {$multiplicador} = {$valor} (soma: {$soma})\n";
    }

    $digitoCalculado = (10 - ($soma % 10)) % 10;
    $digitoFornecido = intval($codigoLimpo[12]);

    echo "\nResultado:\n";
    echo "Soma total: {$soma}\n";
    echo "Resto da divisão por 10: " . ($soma % 10) . "\n";
    echo "Dígito calculado: {$digitoCalculado}\n";
    echo "Dígito fornecido: {$digitoFornecido}\n";
    echo "Válido: " . ($digitoCalculado === $digitoFornecido ? 'Sim' : 'Não') . "\n";

    if ($digitoCalculado !== $digitoFornecido) {
        $codigoCorreto = substr($codigoLimpo, 0, 12) . $digitoCalculado;
        echo "Código correto seria: {$codigoCorreto}\n";
    }
}

// Testar alguns códigos válidos
echo "\n=== TESTANDO CÓDIGOS VÁLIDOS ===\n";
$codigosValidos = [
    '7891234567890' // Este já existe e está funcionando
];

foreach ($codigosValidos as $codigoTeste) {
    $teste = new App\Models\ProdutoCodigoBarras([
        'tipo' => 'ean13',
        'codigo' => $codigoTeste
    ]);

    echo "Código: {$codigoTeste} - Válido: " . ($teste->isValido() ? 'Sim' : 'Não') . "\n";
}

echo "\n=== SUGESTÕES DE CÓDIGOS VÁLIDOS ===\n";
// Gerar alguns códigos válidos baseados no padrão
$base = '789123456789';
for ($i = 0; $i < 10; $i++) {
    $codigoTeste = $base . $i;
    $teste = new App\Models\ProdutoCodigoBarras([
        'tipo' => 'ean13',
        'codigo' => $codigoTeste
    ]);

    if ($teste->isValido()) {
        echo "Código válido encontrado: {$codigoTeste}\n";
    }
}
