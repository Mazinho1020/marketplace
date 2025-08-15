<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Financial\LancamentoFinanceiro;
use Illuminate\Support\Facades\DB;

echo 'Verificando lançamento ID 388...' . PHP_EOL;

// Verificar se existe
$existe = DB::table('lancamentos')->where('id', 388)->first();
if ($existe) {
    echo 'Registro existe:' . PHP_EOL;
    echo '  ID: ' . $existe->id . PHP_EOL;
    echo '  Empresa ID: ' . $existe->empresa_id . PHP_EOL;
    echo '  Natureza: ' . $existe->natureza_financeira . PHP_EOL;
    echo '  Situação: ' . $existe->situacao_financeira . PHP_EOL;
    echo '  Descrição: ' . $existe->descricao . PHP_EOL;
} else {
    echo 'Registro não encontrado!' . PHP_EOL;
}

// Verificar quantos registros existem
echo 'Total de registros: ' . DB::table('lancamentos')->count() . PHP_EOL;
echo 'Registros com empresa_id = 1: ' . DB::table('lancamentos')->where('empresa_id', 1)->count() . PHP_EOL;
echo 'IDs existentes: ' . implode(', ', DB::table('lancamentos')->pluck('id')->toArray()) . PHP_EOL;
