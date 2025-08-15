<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 TESTANDO APIS DE RECEBIMENTO\n";
echo "===============================\n\n";

try {

    // 1. Testar consulta de formas de pagamento para recebimento
    echo "📋 1. Testando formas de pagamento para recebimento:\n";

    $formasRecebimento = DB::table('formas_pagamento')
        ->where('tipo', 'recebimento')
        ->where('empresa_id', 1)
        ->where('ativo', true)
        ->select('id', 'nome', 'tipo')
        ->get();

    echo "   Total encontrado: {$formasRecebimento->count()}\n";

    foreach ($formasRecebimento->take(5) as $forma) {
        echo "   ✅ {$forma->nome} (ID: {$forma->id})\n";
    }

    if ($formasRecebimento->count() > 5) {
        echo "   ... e mais " . ($formasRecebimento->count() - 5) . " formas\n";
    }

    // 2. Testar consulta de bandeiras para uma forma específica
    echo "\n🏷️ 2. Testando bandeiras para PIX (ID: 21):\n";

    $bandeiras = DB::table('forma_pag_bandeiras as fpb')
        ->select('fpb.id', 'fpb.nome', 'fpb.taxa', 'fpb.dias_para_receber')
        ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
        ->where('fpbr.forma_pagamento_id', 21) // PIX
        ->where('fpb.empresa_id', 1)
        ->where('fpb.ativo', true)
        ->get();

    echo "   Total de bandeiras PIX: {$bandeiras->count()}\n";

    foreach ($bandeiras as $bandeira) {
        echo "   🏷️ {$bandeira->nome} - Taxa: {$bandeira->taxa}% - {$bandeira->dias_para_receber} dias\n";
    }

    // 3. Testar simulação da API HTTP
    echo "\n🌐 3. Simulando resposta da API HTTP:\n";

    // Simular o retorno da API de formas de pagamento
    $apiResponse = $formasRecebimento->map(function ($forma) {
        return [
            'id' => $forma->id,
            'nome' => $forma->nome
        ];
    })->toArray();

    echo "   JSON Response para formas de pagamento:\n";
    echo "   " . json_encode($apiResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // 4. Simular resposta da API de bandeiras
    echo "\n   JSON Response para bandeiras do PIX:\n";

    $bandeirasResponse = $bandeiras->map(function ($bandeira) {
        return [
            'id' => $bandeira->id,
            'nome' => $bandeira->nome,
            'taxa' => $bandeira->taxa,
            'dias_para_receber' => $bandeira->dias_para_receber
        ];
    })->toArray();

    echo "   " . json_encode($bandeirasResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // 5. Verificar se o sistema está pronto
    echo "\n✅ 4. Status do sistema:\n";

    $status = [
        'formas_recebimento' => $formasRecebimento->count() > 0,
        'bandeiras_disponíveis' => $bandeiras->count() > 0,
        'relacionamentos_ok' => DB::table('forma_pagamento_bandeiras')->count() > 0
    ];

    foreach ($status as $item => $ok) {
        $icon = $ok ? '✅' : '❌';
        echo "   {$icon} {$item}: " . ($ok ? 'OK' : 'PROBLEMA') . "\n";
    }

    if (array_filter($status) === $status) {
        echo "\n🎉 SISTEMA TOTALMENTE FUNCIONAL PARA RECEBIMENTOS!\n";
        echo "   - Formas de pagamento: {$formasRecebimento->count()}\n";
        echo "   - Bandeiras configuradas: " . DB::table('forma_pag_bandeiras')->where('empresa_id', 1)->count() . "\n";
        echo "   - APIs prontas para uso\n";
    } else {
        echo "\n⚠️ Sistema precisa de ajustes.\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
