<?php
require 'vendor/autoload.php';

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 ANALISANDO DADOS DA TABELA FORMAS_PAGAMENTO\n";
echo "===============================================\n\n";

try {
    // 1. Verificar tipos disponíveis
    echo "📊 1. TIPOS DISPONÍVEIS:\n";
    $tipos = DB::table('formas_pagamento')
        ->where('empresa_id', 1)
        ->groupBy('tipo')
        ->pluck('tipo')
        ->toArray();

    foreach ($tipos as $tipo) {
        echo "  • $tipo\n";
    }

    // 2. Verificar origens disponíveis
    echo "\n📊 2. ORIGENS DISPONÍVEIS:\n";
    $origens = DB::table('formas_pagamento')
        ->where('empresa_id', 1)
        ->groupBy('origem')
        ->pluck('origem')
        ->toArray();

    foreach ($origens as $origem) {
        echo "  • $origem\n";
    }

    // 3. Formas que passariam no filtro atual (tipo=recebimento, origem=sistema/pdv)
    echo "\n🎯 3. FORMAS QUE PASSAM NO FILTRO ATUAL:\n";
    echo "   (ativo=true, empresa_id=1, tipo=recebimento, origem=sistema/pdv)\n";
    $formasComFiltro = DB::table('formas_pagamento')
        ->where('ativo', true)
        ->where('empresa_id', 1)
        ->where('tipo', 'recebimento')
        ->whereIn('origem', ['sistema', 'pdv'])
        ->get(['id', 'nome', 'tipo', 'origem']);

    if ($formasComFiltro->count() > 0) {
        foreach ($formasComFiltro as $forma) {
            echo "  • {$forma->nome} (ID: {$forma->id}, Tipo: {$forma->tipo}, Origem: {$forma->origem})\n";
        }
    } else {
        echo "  ❌ NENHUMA FORMA ENCONTRADA COM ESSES FILTROS!\n";
    }

    // 4. Todas as formas ativas da empresa 1
    echo "\n📋 4. TODAS AS FORMAS ATIVAS DA EMPRESA 1:\n";
    $todasFormas = DB::table('formas_pagamento')
        ->where('ativo', true)
        ->where('empresa_id', 1)
        ->get(['id', 'nome', 'tipo', 'origem']);

    foreach ($todasFormas as $forma) {
        echo "  • {$forma->nome} (Tipo: {$forma->tipo}, Origem: {$forma->origem})\n";
    }

    echo "\n✅ ANÁLISE CONCLUÍDA!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
