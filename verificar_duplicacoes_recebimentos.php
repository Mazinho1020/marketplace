<?php

require_once 'vendor/autoload.php';

// Inicializar aplicação Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Financial\Pagamento;

echo "🔍 VERIFICAÇÃO DE DUPLICAÇÕES EM RECEBIMENTOS\n";
echo "===========================================\n\n";

try {
    // Buscar recebimentos (tipo_id = 2) agrupados por lançamento
    $recebimentosAgrupados = DB::table('pagamentos')
        ->select('lancamento_id', 'valor', 'data_pagamento', 'forma_pagamento_id', DB::raw('COUNT(*) as total'))
        ->where('tipo_id', 2) // 2 = recebimento
        ->where('status_pagamento', 'confirmado')
        ->groupBy('lancamento_id', 'valor', 'data_pagamento', 'forma_pagamento_id')
        ->having('total', '>', 1)
        ->get();

    if ($recebimentosAgrupados->count() > 0) {
        echo "❌ ENCONTRADAS {$recebimentosAgrupados->count()} DUPLICAÇÕES:\n\n";

        foreach ($recebimentosAgrupados as $grupo) {
            echo "📋 Lançamento ID: {$grupo->lancamento_id}\n";
            echo "   💰 Valor: R$ " . number_format($grupo->valor, 2, ',', '.') . "\n";
            echo "   📅 Data: {$grupo->data_pagamento}\n";
            echo "   🔢 Forma Pagamento ID: {$grupo->forma_pagamento_id}\n";
            echo "   🔴 Total de duplicatas: {$grupo->total}\n";

            // Buscar os IDs específicos das duplicatas
            $duplicatas = DB::table('pagamentos')
                ->select('id', 'created_at')
                ->where('lancamento_id', $grupo->lancamento_id)
                ->where('tipo_id', 2)
                ->where('valor', $grupo->valor)
                ->where('data_pagamento', $grupo->data_pagamento)
                ->where('forma_pagamento_id', $grupo->forma_pagamento_id)
                ->where('status_pagamento', 'confirmado')
                ->orderBy('created_at', 'asc')
                ->get();

            echo "   📋 IDs das duplicatas:\n";
            foreach ($duplicatas as $index => $dup) {
                $status = $index === 0 ? '✅ (manter)' : '🗑️ (remover)';
                echo "      - ID {$dup->id} criado em {$dup->created_at} {$status}\n";
            }
            echo "\n";
        }

        // Pergunta se deve remover as duplicatas
        echo "🤔 Deseja remover as duplicatas automaticamente? (s/N): ";
        $resposta = trim(fgets(STDIN));

        if (strtolower($resposta) === 's') {
            DB::beginTransaction();

            try {
                $totalRemovidos = 0;

                foreach ($recebimentosAgrupados as $grupo) {
                    // Buscar as duplicatas para este grupo
                    $duplicatas = DB::table('pagamentos')
                        ->select('id')
                        ->where('lancamento_id', $grupo->lancamento_id)
                        ->where('tipo_id', 2)
                        ->where('valor', $grupo->valor)
                        ->where('data_pagamento', $grupo->data_pagamento)
                        ->where('forma_pagamento_id', $grupo->forma_pagamento_id)
                        ->where('status_pagamento', 'confirmado')
                        ->orderBy('created_at', 'asc')
                        ->get();

                    // Manter o primeiro (mais antigo) e remover os demais
                    foreach ($duplicatas->skip(1) as $duplicata) {
                        DB::table('pagamentos')
                            ->where('id', $duplicata->id)
                            ->update([
                                'status_pagamento' => 'cancelado',
                                'observacao' => DB::raw("CONCAT(COALESCE(observacao, ''), '\\n[DUPLICATA REMOVIDA AUTOMATICAMENTE EM " . date('Y-m-d H:i:s') . "]')")
                            ]);
                        $totalRemovidos++;
                        echo "🗑️ Removido pagamento ID {$duplicata->id}\n";
                    }
                }

                DB::commit();
                echo "✅ {$totalRemovidos} duplicatas removidas com sucesso!\n";
            } catch (\Exception $e) {
                DB::rollback();
                echo "❌ Erro ao remover duplicatas: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ℹ️ Duplicatas mantidas. Execute novamente se quiser removê-las.\n";
        }
    } else {
        echo "✅ NENHUMA DUPLICAÇÃO ENCONTRADA!\n";
        echo "👍 Todos os recebimentos estão únicos.\n";
    }

    // Estatísticas gerais
    echo "\n📊 ESTATÍSTICAS GERAIS:\n";
    echo "========================\n";

    $totalRecebimentos = DB::table('pagamentos')
        ->where('tipo_id', 2)
        ->where('status_pagamento', 'confirmado')
        ->count();

    $valorTotalRecebimentos = DB::table('pagamentos')
        ->where('tipo_id', 2)
        ->where('status_pagamento', 'confirmado')
        ->sum('valor');

    $lancamentosComRecebimento = DB::table('pagamentos')
        ->where('tipo_id', 2)
        ->where('status_pagamento', 'confirmado')
        ->distinct('lancamento_id')
        ->count();

    echo "📋 Total de recebimentos: {$totalRecebimentos}\n";
    echo "💰 Valor total recebido: R$ " . number_format($valorTotalRecebimentos, 2, ',', '.') . "\n";
    echo "🏢 Lançamentos com recebimento: {$lancamentosComRecebimento}\n";
} catch (\Exception $e) {
    echo "❌ Erro na verificação: " . $e->getMessage() . "\n";
    echo "📋 Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🎉 Verificação concluída!\n";
