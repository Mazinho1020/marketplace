<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🏷️ INSERINDO BANDEIRAS COMPLEMENTARES PARA RECEBIMENTOS\n";
echo "======================================================\n\n";

try {

    // Definir bandeiras complementares
    $bandeirasPorForma = [
        'Boleto Bancário' => [
            ['nome' => 'Boleto Banco do Brasil', 'taxa' => 2.50, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Itaú', 'taxa' => 2.80, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Bradesco', 'taxa' => 2.75, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Santander', 'taxa' => 2.60, 'dias_para_receber' => 1],
        ],
        'Cheque' => [
            ['nome' => 'Cheque à Vista', 'taxa' => 0.00, 'dias_para_receber' => 1],
            ['nome' => 'Cheque Pré-datado 30 dias', 'taxa' => 0.00, 'dias_para_receber' => 30],
            ['nome' => 'Cheque Pré-datado 60 dias', 'taxa' => 0.00, 'dias_para_receber' => 60],
        ],
        'TED' => [
            ['nome' => 'TED Banco do Brasil', 'taxa' => 5.00, 'dias_para_receber' => 1],
            ['nome' => 'TED Itaú', 'taxa' => 8.00, 'dias_para_receber' => 1],
            ['nome' => 'TED Bradesco', 'taxa' => 7.50, 'dias_para_receber' => 1],
            ['nome' => 'TED Santander', 'taxa' => 6.90, 'dias_para_receber' => 1],
        ],
        'DOC' => [
            ['nome' => 'DOC Banco do Brasil', 'taxa' => 3.50, 'dias_para_receber' => 1],
            ['nome' => 'DOC Itaú', 'taxa' => 4.00, 'dias_para_receber' => 1],
            ['nome' => 'DOC Bradesco', 'taxa' => 3.75, 'dias_para_receber' => 1],
        ],
        'Depósito Bancário' => [
            ['nome' => 'Depósito BB', 'taxa' => 0.00, 'dias_para_receber' => 1],
            ['nome' => 'Depósito Itaú', 'taxa' => 0.00, 'dias_para_receber' => 1],
            ['nome' => 'Depósito Bradesco', 'taxa' => 0.00, 'dias_para_receber' => 1],
            ['nome' => 'Depósito Santander', 'taxa' => 0.00, 'dias_para_receber' => 1],
            ['nome' => 'Depósito Caixa', 'taxa' => 0.00, 'dias_para_receber' => 1],
        ],
        'Dinheiro' => [
            ['nome' => 'Dinheiro - Recebimento Direto', 'taxa' => 0.00, 'dias_para_receber' => 0],
        ],
        // Formas online específicas
        'PIX Online' => [
            ['nome' => 'PIX Gateway PagSeguro', 'taxa' => 0.99, 'dias_para_receber' => 0],
            ['nome' => 'PIX Gateway Mercado Pago', 'taxa' => 1.19, 'dias_para_receber' => 0],
            ['nome' => 'PIX Gateway Stone', 'taxa' => 0.79, 'dias_para_receber' => 0],
        ],
        'Cartão de Crédito Online' => [
            ['nome' => 'Visa Online', 'taxa' => 3.49, 'dias_para_receber' => 30],
            ['nome' => 'Mastercard Online', 'taxa' => 3.49, 'dias_para_receber' => 30],
            ['nome' => 'Elo Online', 'taxa' => 3.29, 'dias_para_receber' => 30],
        ],
        'Cartão de Débito Online' => [
            ['nome' => 'Visa Débito Online', 'taxa' => 1.99, 'dias_para_receber' => 1],
            ['nome' => 'Mastercard Débito Online', 'taxa' => 1.99, 'dias_para_receber' => 1],
            ['nome' => 'Elo Débito Online', 'taxa' => 1.89, 'dias_para_receber' => 1],
        ],
        'Boleto Online' => [
            ['nome' => 'Boleto Online PagSeguro', 'taxa' => 3.49, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Online Mercado Pago', 'taxa' => 3.99, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Online Stone', 'taxa' => 2.99, 'dias_para_receber' => 1],
        ],
        'Cartão na Máquina (Entregador)' => [
            ['nome' => 'Visa Maquininha', 'taxa' => 2.69, 'dias_para_receber' => 1],
            ['nome' => 'Mastercard Maquininha', 'taxa' => 2.69, 'dias_para_receber' => 1],
            ['nome' => 'Elo Maquininha', 'taxa' => 2.59, 'dias_para_receber' => 1],
        ],
        'Dinheiro (Entregador)' => [
            ['nome' => 'Dinheiro Entrega', 'taxa' => 0.00, 'dias_para_receber' => 0],
        ],
        'PIX na Hora (Entregador)' => [
            ['nome' => 'PIX Entregador', 'taxa' => 0.00, 'dias_para_receber' => 0],
        ]
    ];

    // Buscar formas de recebimento
    $formasRecebimento = DB::table('formas_pagamento')
        ->where('tipo', 'recebimento')
        ->where('empresa_id', 1)
        ->get()
        ->keyBy('nome');

    echo "💳 Inserindo bandeiras complementares...\n\n";

    $totalInseridas = 0;

    foreach ($bandeirasPorForma as $nomeForma => $bandeiras) {
        if (isset($formasRecebimento[$nomeForma])) {
            $forma = $formasRecebimento[$nomeForma];
            echo "🎯 Processando {$nomeForma}:\n";

            foreach ($bandeiras as $bandeiraData) {
                // Verificar se a bandeira já existe
                $bandeirasExiste = DB::table('forma_pag_bandeiras')
                    ->where('empresa_id', 1)
                    ->where('nome', $bandeiraData['nome'])
                    ->exists();

                if ($bandeirasExiste) {
                    echo "   ⚠️  {$bandeiraData['nome']} já existe\n";
                    continue;
                }

                // Inserir bandeira
                $bandeiraId = DB::table('forma_pag_bandeiras')->insertGetId([
                    'empresa_id' => 1,
                    'nome' => $bandeiraData['nome'],
                    'taxa' => $bandeiraData['taxa'],
                    'dias_para_receber' => $bandeiraData['dias_para_receber'],
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Relacionar forma de pagamento com bandeira
                DB::table('forma_pagamento_bandeiras')->insert([
                    'forma_pagamento_id' => $forma->id,
                    'forma_pag_bandeira_id' => $bandeiraId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                echo "   ✅ {$bandeiraData['nome']} (Taxa: {$bandeiraData['taxa']}% - {$bandeiraData['dias_para_receber']} dias)\n";
                $totalInseridas++;
            }
            echo "\n";
        } else {
            echo "⚠️  Forma não encontrada: {$nomeForma}\n\n";
        }
    }

    echo "📊 Resumo final:\n";
    echo "   Total de bandeiras inseridas: {$totalInseridas}\n";

    // Verificar resultado final
    $bandeirasTotal = DB::table('forma_pag_bandeiras')
        ->where('empresa_id', 1)
        ->count();

    echo "   Total de bandeiras no banco: {$bandeirasTotal}\n\n";

    // Mostrar estatísticas por forma
    echo "📈 Estatísticas finais por forma de pagamento:\n";
    foreach ($formasRecebimento as $forma) {
        $bandeirasCount = DB::table('forma_pag_bandeiras as fpb')
            ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
            ->where('fpbr.forma_pagamento_id', $forma->id)
            ->where('fpb.empresa_id', 1)
            ->count();

        echo "   {$forma->nome}: {$bandeirasCount} bandeiras\n";
    }

    echo "\n🎉 BANDEIRAS COMPLEMENTARES INSERIDAS COM SUCESSO!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
