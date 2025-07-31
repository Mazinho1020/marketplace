<?php
// Arquivo de teste para Xdebug
require_once __DIR__ . '/vendor/autoload.php';

echo "Início do script\n";

// Teste simples sem dependências do Laravel
$name = "Marketplace";
$version = "1.0.0";
$debug = true;

echo "Nome: " . $name . "\n";
echo "Versão: " . $version . "\n";
echo "Debug: " . ($debug ? 'Ativo' : 'Inativo') . "\n";

// Teste de array
$config = [
    'app' => $name,
    'version' => $version,
    'debug' => $debug
];

foreach ($config as $key => $value) {
    echo "Config[$key] = " . $value . "\n";
}

echo "Teste do Xdebug concluído!\n";
