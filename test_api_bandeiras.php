<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTE DAS APIS DE FORMAS DE PAGAMENTO E BANDEIRAS\n";
echo "=====================================\n\n";

try {
    // 1. Testar API de formas de pagamento
    echo "📋 1. FORMAS DE PAGAMENTO DISPONÍVEIS:\n";
    echo "---------------------------------------\n";

    $formasPagamento = DB::table('formas_pagamento')
        ->where('ativo', true)
        ->orderBy('nome')
        ->get(['id', 'nome', 'gateway_method']);

    foreach ($formasPagamento as $index => $forma) {
        echo sprintf("%d. %s (ID: %d)\n", $index + 1, $forma->nome, $forma->id);
    }

    echo "\n🏷️ 2. BANDEIRAS POR FORMA DE PAGAMENTO:\n";
    echo "----------------------------------------\n";

    // 2. Para cada forma, buscar suas bandeiras
    foreach ($formasPagamento as $forma) {
        echo "\n🔸 {$forma->nome} (ID: {$forma->id}):\n";

        $bandeiras = DB::table('forma_pag_bandeiras as fpb')
            ->select(['fpb.id', 'fpb.nome', 'fpb.dias_para_receber', 'fpb.taxa'])
            ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
            ->where('fpbr.forma_pagamento_id', $forma->id)
            ->where('fpb.ativo', true)
            ->orderBy('fpb.nome')
            ->get();

        if ($bandeiras->count() > 0) {
            foreach ($bandeiras as $bandeira) {
                echo sprintf(
                    "   • %s (Taxa: %s%% - %d dias) [ID: %d]\n",
                    $bandeira->nome,
                    $bandeira->taxa,
                    $bandeira->dias_para_receber,
                    $bandeira->id
                );
            }
        } else {
            echo "   ⚠️ Nenhuma bandeira configurada\n";
        }
    }

    echo "\n📊 3. ESTATÍSTICAS:\n";
    echo "-------------------\n";
    echo sprintf("• Total de formas ativas: %d\n", $formasPagamento->count());

    $totalBandeiras = DB::table('forma_pag_bandeiras')
        ->where('ativo', true)
        ->count();
    echo sprintf("• Total de bandeiras ativas: %d\n", $totalBandeiras);

    $totalRelacionamentos = DB::table('forma_pagamento_bandeiras')->count();
    echo sprintf("• Total de relacionamentos: %d\n", $totalRelacionamentos);

    echo "\n✅ TESTE CONCLUÍDO COM SUCESSO!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . " (Linha: " . $e->getLine() . ")\n";
}
