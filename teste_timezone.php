<?php

require 'vendor/autoload.php';

use Carbon\Carbon;

echo "=== TESTE DE TIMEZONE ===\n";
echo "PHP timezone padrão: " . date_default_timezone_get() . "\n";
echo "Horário atual (PHP): " . date('Y-m-d H:i:s') . "\n\n";

// Definir timezone para Cuiabá
date_default_timezone_set('America/Cuiaba');
echo "Após definir America/Cuiaba:\n";
echo "PHP timezone: " . date_default_timezone_get() . "\n";
echo "Horário atual (PHP): " . date('Y-m-d H:i:s') . "\n\n";

// Testar Carbon
echo "Carbon sem timezone específico: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
echo "Carbon com America/Cuiaba: " . Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s') . "\n";
echo "Carbon setando timezone global: ";
Carbon::setLocale('pt_BR');
echo Carbon::now()->format('Y-m-d H:i:s') . "\n";

echo "\n=== DIFERENÇAS DE TIMEZONE ===\n";
echo "UTC: " . Carbon::now('UTC')->format('Y-m-d H:i:s') . "\n";
echo "America/Cuiaba: " . Carbon::now('America/Cuiaba')->format('Y-m-d H:i:s') . "\n";
echo "Europe/Berlin: " . Carbon::now('Europe/Berlin')->format('Y-m-d H:i:s') . "\n";
