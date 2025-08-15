<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Buscando tabelas relacionadas a lançamentos financeiros...\n";

$tabelas = DB::select('SHOW TABLES');
$database = DB::getDatabaseName();

echo "📋 Tabelas que contêm 'lancament' ou 'financ':\n";
foreach ($tabelas as $tabela) {
    $nomeTabela = $tabela->{"Tables_in_" . $database};
    if (stripos($nomeTabela, 'lancament') !== false || stripos($nomeTabela, 'financ') !== false) {
        echo "  - {$nomeTabela}\n";
    }
}

echo "\n📋 Tabelas que contêm 'conta' ou 'receb':\n";
foreach ($tabelas as $tabela) {
    $nomeTabela = $tabela->{"Tables_in_" . $database};
    if (stripos($nomeTabela, 'conta') !== false || stripos($nomeTabela, 'receb') !== false) {
        echo "  - {$nomeTabela}\n";
    }
}

echo "\n📋 Tabelas que contêm 'pagar':\n";
foreach ($tabelas as $tabela) {
    $nomeTabela = $tabela->{"Tables_in_" . $database};
    if (stripos($nomeTabela, 'pagar') !== false) {
        echo "  - {$nomeTabela}\n";
    }
}
