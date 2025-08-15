#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTE DA API DE FORMAS DE PAGAMENTO ===\n\n";

// Obter uma empresa para testar
$empresa = DB::table('empresas')->first();
if (!$empresa) {
    echo "❌ Nenhuma empresa encontrada!\n";
    exit(1);
}

echo "Testando com empresa ID: {$empresa->id} - {$empresa->nome_fantasia}\n\n";

// Teste 1: Todas as formas de pagamento
echo "1. Total de formas de pagamento: " . DB::table('formas_pagamento')->count() . "\n";

// Teste 2: Formas ativas
echo "2. Formas de pagamento ativas: " . DB::table('formas_pagamento')->where('ativo', true)->count() . "\n";

// Teste 3: Formas do tipo pagamento
echo "3. Formas do tipo 'pagamento': " . DB::table('formas_pagamento')->where('tipo', 'pagamento')->count() . "\n";

// Teste 4: Formas da empresa específica
echo "4. Formas da empresa {$empresa->id}: " . DB::table('formas_pagamento')->where('empresa_id', $empresa->id)->count() . "\n";

// Teste 5: Simulação da consulta exata da API
$formasPagamento = DB::table('formas_pagamento')
    ->where('ativo', true)
    ->where('empresa_id', $empresa->id)
    ->where('tipo', 'pagamento')
    ->whereIn('origem', ['sistema'])
    ->where('is_gateway', 0)
    ->orderBy('nome')
    ->get(['id', 'nome', 'gateway_method', 'tipo', 'origem']);

echo "5. Formas que a API retornaria: " . $formasPagamento->count() . "\n";

if ($formasPagamento->count() > 0) {
    echo "\nFormas encontradas:\n";
    foreach ($formasPagamento as $forma) {
        echo "   - ID: {$forma->id} | Nome: {$forma->nome} | Tipo: {$forma->tipo} | Origem: {$forma->origem}\n";
    }
} else {
    echo "\n❌ PROBLEMA: Nenhuma forma de pagamento encontrada com os critérios da API!\n";

    // Vamos investigar o que há disponível
    echo "\nInvestigando dados disponíveis:\n";

    $todasFormas = DB::table('formas_pagamento')
        ->where('empresa_id', $empresa->id)
        ->get(['id', 'nome', 'tipo', 'origem', 'ativo', 'is_gateway']);

    if ($todasFormas->count() > 0) {
        echo "Formas da empresa {$empresa->id}:\n";
        foreach ($todasFormas as $forma) {
            echo "   - ID: {$forma->id} | Nome: {$forma->nome} | Tipo: {$forma->tipo} | Origem: {$forma->origem} | Ativo: " . ($forma->ativo ? 'Sim' : 'Não') . " | Gateway: " . ($forma->is_gateway ? 'Sim' : 'Não') . "\n";
        }
    } else {
        echo "Nenhuma forma de pagamento encontrada para esta empresa.\n";

        // Verificar se há formas globais (empresa_id = 0 ou null)
        $formasGlobais = DB::table('formas_pagamento')
            ->whereNull('empresa_id')
            ->orWhere('empresa_id', 0)
            ->get(['id', 'nome', 'tipo', 'origem', 'ativo', 'is_gateway']);

        if ($formasGlobais->count() > 0) {
            echo "\nFormas de pagamento globais encontradas:\n";
            foreach ($formasGlobais as $forma) {
                echo "   - ID: {$forma->id} | Nome: {$forma->nome} | Tipo: {$forma->tipo} | Origem: {$forma->origem} | Ativo: " . ($forma->ativo ? 'Sim' : 'Não') . " | Gateway: " . ($forma->is_gateway ? 'Sim' : 'Não') . "\n";
            }
        }
    }
}

echo "\n=== URL PARA TESTE MANUAL ===\n";
echo "http://127.0.0.1:8000/comerciantes/empresas/{$empresa->id}/financeiro/api/formas-pagamento-saida\n";
