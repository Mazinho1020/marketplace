<?php
echo "Teste básico PHP\n";
echo "Version: " . phpversion() . "\n";
echo "Working dir: " . getcwd() . "\n";
echo "Autoload existe: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'Sim' : 'Não') . "\n";
