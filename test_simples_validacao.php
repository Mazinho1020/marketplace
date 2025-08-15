<?php

echo "=== TESTE SIMPLES VALIDAÇÃO ===\n";

// Simular valores exatos
$valor_request = 300.00;
$saldo_devedor = 300.00;

echo "Valor request: $valor_request\n";
echo "Saldo devedor: $saldo_devedor\n";

echo "\nTestes de comparação:\n";
echo "300.00 > 300.00 = " . ($valor_request > $saldo_devedor ? 'TRUE' : 'FALSE') . "\n";
echo "300 > 300 = " . (300 > 300 ? 'TRUE' : 'FALSE') . "\n";

// Testar com strings (como vem do formulário)
$valor_string = "300";
$saldo_string = "300.00";

echo "\nTeste com strings:\n";
echo "\"$valor_string\" > \"$saldo_string\" = " . ($valor_string > $saldo_string ? 'TRUE' : 'FALSE') . "\n";
echo "(float)\"$valor_string\" > (float)\"$saldo_string\" = " . ((float)$valor_string > (float)$saldo_string ? 'TRUE' : 'FALSE') . "\n";

// Testar diferentes precisões
$valor1 = 300.000000001;
$valor2 = 300.000000000;

echo "\nTeste com precisão:\n";
echo "$valor1 > $valor2 = " . ($valor1 > $valor2 ? 'TRUE' : 'FALSE') . "\n";

echo "\n=== FIM TESTE ===\n";
