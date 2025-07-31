<?php
echo "Laravel Marketplace - Teste de Conectividade\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Diretório atual: " . __DIR__ . "\n";
echo "Existe public/index.php: " . (file_exists('public/index.php') ? 'SIM' : 'NÃO') . "\n";

if (file_exists('public/index.php')) {
    echo "Redirecionando para Laravel...\n";
    require_once 'public/index.php';
} else {
    echo "Arquivo public/index.php não encontrado!\n";
}
