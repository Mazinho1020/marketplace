<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🏷️ INSERINDO BANDEIRAS PARA RECEBIMENTOS\n";
echo "==========================================\n\n";

try {
    // Verificar se as tabelas existem
    echo "📋 1. Verificando estrutura das tabelas...\n";

    $tables = DB::select("SHOW TABLES LIKE '%bandeira%'");
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "   ✅ Tabela encontrada: {$tableName}\n";
    }

    // Verificar estrutura da tabela forma_pag_bandeiras
    echo "\n📊 2. Estrutura da tabela forma_pag_bandeiras:\n";
    $columns = DB::select("SHOW COLUMNS FROM forma_pag_bandeiras");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type})\n";
    }

    // Buscar IDs das formas de pagamento de recebimento
    echo "\n🔍 3. Buscando formas de recebimento...\n";
    $formasRecebimento = DB::table('formas_pagamento')
        ->where('tipo', 'recebimento')
        ->where('empresa_id', 1)
        ->get();

    if ($formasRecebimento->isEmpty()) {
        echo "❌ Nenhuma forma de recebimento encontrada! Execute primeiro o script de formas de pagamento.\n";
        exit(1);
    }

    foreach ($formasRecebimento as $forma) {
        echo "   ✅ {$forma->nome} (ID: {$forma->id})\n";
    }

    // Verificar se já existem bandeiras
    echo "\n🔍 4. Verificando bandeiras existentes...\n";
    $bandeirasExistentes = DB::table('forma_pag_bandeiras')
        ->where('empresa_id', 1)
        ->count();

    echo "   Bandeiras existentes: {$bandeirasExistentes}\n";

    // Definir bandeiras por forma de pagamento
    $bandeirasPorForma = [
        'PIX' => [
            ['nome' => 'PIX Banco do Brasil', 'taxa' => 0.00, 'dias_para_receber' => 0],
            ['nome' => 'PIX Itaú', 'taxa' => 0.00, 'dias_para_receber' => 0],
            ['nome' => 'PIX Bradesco', 'taxa' => 0.00, 'dias_para_receber' => 0],
            ['nome' => 'PIX Santander', 'taxa' => 0.00, 'dias_para_receber' => 0],
            ['nome' => 'PIX Caixa', 'taxa' => 0.00, 'dias_para_receber' => 0],
        ],
        'Cartão de Crédito' => [
            ['nome' => 'Visa', 'taxa' => 2.79, 'dias_para_receber' => 30],
            ['nome' => 'Mastercard', 'taxa' => 2.79, 'dias_para_receber' => 30],
            ['nome' => 'American Express', 'taxa' => 3.99, 'dias_para_receber' => 30],
            ['nome' => 'Elo', 'taxa' => 2.79, 'dias_para_receber' => 30],
            ['nome' => 'Hipercard', 'taxa' => 2.99, 'dias_para_receber' => 30],
        ],
        'Cartão de Débito' => [
            ['nome' => 'Visa Débito', 'taxa' => 1.39, 'dias_para_receber' => 1],
            ['nome' => 'Mastercard Débito', 'taxa' => 1.39, 'dias_para_receber' => 1],
            ['nome' => 'Elo Débito', 'taxa' => 1.39, 'dias_para_receber' => 1],
        ],
        'Transferência Bancária' => [
            ['nome' => 'TED Banco do Brasil', 'taxa' => 5.00, 'dias_para_receber' => 1],
            ['nome' => 'TED Itaú', 'taxa' => 8.00, 'dias_para_receber' => 1],
            ['nome' => 'TED Bradesco', 'taxa' => 7.50, 'dias_para_receber' => 1],
            ['nome' => 'TED Santander', 'taxa' => 6.90, 'dias_para_receber' => 1],
            ['nome' => 'DOC', 'taxa' => 3.50, 'dias_para_receber' => 1],
        ],
        'Boleto' => [
            ['nome' => 'Boleto Bancário BB', 'taxa' => 2.50, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Bancário Itaú', 'taxa' => 2.80, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Bancário Bradesco', 'taxa' => 2.75, 'dias_para_receber' => 1],
            ['nome' => 'Boleto Registrado', 'taxa' => 1.99, 'dias_para_receber' => 1],
        ]
    ];

    echo "\n💳 5. Inserindo bandeiras...\n";

    $totalInseridas = 0;

    foreach ($formasRecebimento as $forma) {
        if (isset($bandeirasPorForma[$forma->nome])) {
            echo "\n   🎯 Processando {$forma->nome}:\n";

            foreach ($bandeirasPorForma[$forma->nome] as $bandeiraData) {
                // Verificar se a bandeira já existe
                $bandeirasExiste = DB::table('forma_pag_bandeiras')
                    ->where('empresa_id', 1)
                    ->where('nome', $bandeiraData['nome'])
                    ->exists();

                if ($bandeirasExiste) {
                    echo "      ⚠️  {$bandeiraData['nome']} já existe\n";
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

                // Verificar se existe tabela de relacionamento
                $tabelaRelacionamento = DB::select("SHOW TABLES LIKE 'forma_pagamento_bandeiras'");

                if (!empty($tabelaRelacionamento)) {
                    // Relacionar forma de pagamento com bandeira
                    DB::table('forma_pagamento_bandeiras')->insert([
                        'forma_pagamento_id' => $forma->id,
                        'forma_pag_bandeira_id' => $bandeiraId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                echo "      ✅ {$bandeiraData['nome']} (Taxa: {$bandeiraData['taxa']}% - {$bandeiraData['dias_para_receber']} dias)\n";
                $totalInseridas++;
            }
        } else {
            echo "   ⚠️  Bandeiras não definidas para: {$forma->nome}\n";
        }
    }

    echo "\n📊 6. Resumo final:\n";
    echo "   Total de bandeiras inseridas: {$totalInseridas}\n";

    // Verificar resultado
    $bandeirasTotal = DB::table('forma_pag_bandeiras')
        ->where('empresa_id', 1)
        ->count();

    echo "   Total de bandeiras no banco: {$bandeirasTotal}\n";

    // Testar consulta de bandeiras por forma
    echo "\n🧪 7. Testando consulta por forma:\n";

    foreach ($formasRecebimento->take(2) as $forma) {
        $bandeirasForma = DB::table('forma_pag_bandeiras as fpb')
            ->select('fpb.id', 'fpb.nome', 'fpb.taxa', 'fpb.dias_para_receber')
            ->join('forma_pagamento_bandeiras as fpbr', 'fpb.id', '=', 'fpbr.forma_pag_bandeira_id')
            ->where('fpbr.forma_pagamento_id', $forma->id)
            ->where('fpb.empresa_id', 1)
            ->where('fpb.ativo', true)
            ->get();

        echo "   {$forma->nome}: {$bandeirasForma->count()} bandeiras\n";
        foreach ($bandeirasForma->take(3) as $bandeira) {
            echo "      - {$bandeira->nome} ({$bandeira->taxa}%)\n";
        }
    }

    echo "\n🎉 BANDEIRAS INSERIDAS COM SUCESSO!\n";
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
