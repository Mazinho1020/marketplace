<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 VERIFICANDO BANDEIRAS DO BOLETO BANCÁRIO\n";
echo "===========================================\n\n";

try {

    // Verificar se existem bandeiras para Boleto Bancário (ID: 26)
    echo "📋 Bandeiras para Boleto Bancário (ID: 26):\n";

    $bandeiras = DB::table('forma_pag_bandeiras as fpb')
        ->select('fpb.id', 'fpb.nome', 'fpb.taxa', 'fpb.dias_para_receber')
        ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
        ->where('fpbr.forma_pagamento_id', 26)
        ->where('fpb.empresa_id', 1)
        ->where('fpb.ativo', true)
        ->get();

    echo "   Total encontrado: {$bandeiras->count()}\n\n";

    if ($bandeiras->count() > 0) {
        foreach ($bandeiras as $bandeira) {
            echo "   🏷️ {$bandeira->nome} - Taxa: {$bandeira->taxa}% - {$bandeira->dias_para_receber} dias\n";
        }
    } else {
        echo "   ❌ Nenhuma bandeira encontrada!\n";

        // Verificar se as bandeiras existem na tabela forma_pag_bandeiras
        echo "\n🔍 Verificando bandeiras na tabela geral:\n";

        $todasBandeiras = DB::table('forma_pag_bandeiras')
            ->where('empresa_id', 1)
            ->where('nome', 'LIKE', '%boleto%')
            ->orWhere('nome', 'LIKE', '%Boleto%')
            ->get();

        foreach ($todasBandeiras as $bandeira) {
            echo "   🏷️ {$bandeira->nome} (ID: {$bandeira->id})\n";
        }

        // Verificar relacionamentos
        echo "\n🔗 Verificando relacionamentos:\n";

        $relacionamentos = DB::table('forma_pagamento_bandeiras')
            ->where('forma_pagamento_id', 26)
            ->get();

        echo "   Total de relacionamentos para Boleto Bancário: {$relacionamentos->count()}\n";

        foreach ($relacionamentos as $rel) {
            echo "   🔗 Forma: {$rel->forma_pagamento_id} -> Bandeira: {$rel->forma_pag_bandeira_id}\n";
        }
    }

    // Testar com PIX que sabemos que tem bandeiras
    echo "\n📋 Testando PIX (ID: 21) para comparação:\n";

    $bandeirasPix = DB::table('forma_pag_bandeiras as fpb')
        ->select('fpb.id', 'fpb.nome', 'fpb.taxa', 'fpb.dias_para_receber')
        ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
        ->where('fpbr.forma_pagamento_id', 21)
        ->where('fpb.empresa_id', 1)
        ->where('fpb.ativo', true)
        ->get();

    echo "   Total PIX: {$bandeirasPix->count()}\n";

    foreach ($bandeirasPix->take(3) as $bandeira) {
        echo "   🏷️ {$bandeira->nome}\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
