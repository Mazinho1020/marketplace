<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Simular o Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTE DO CONTROLLER CUPONS ===\n";

try {
    $cupons = DB::table('fidelidade_cupons as fc')
        ->leftJoin('empresas as e', 'fc.empresa_id', '=', 'e.id')
        ->select(
            'fc.*',
            'e.nome_fantasia as empresa_nome'
        )
        ->orderBy('fc.criado_em', 'desc')
        ->get();

    echo "Total de cupons encontrados: " . $cupons->count() . "\n";

    foreach ($cupons as $cupom) {
        echo "Cupom ID: {$cupom->id}, Código: {$cupom->codigo}, Nome: {$cupom->nome}, Status: {$cupom->status}\n";
    }

    $stats = [
        'total_cupons' => $cupons->count(),
        'cupons_ativos' => $cupons->where('status', 'ativo')->count(),
        'cupons_inativos' => $cupons->where('status', 'inativo')->count(),
        'cupons_usados' => DB::table('fidelidade_cupons_uso')->count()
    ];

    echo "\n=== ESTATÍSTICAS ===\n";
    foreach ($stats as $key => $value) {
        echo "$key: $value\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
