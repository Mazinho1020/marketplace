<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== VERIFICANDO DADOS NA TABELA RECEBIMENTOS ===\n";

    // Contar registros na tabela recebimentos
    $countRecebimentos = DB::table('recebimentos')->count();
    echo "Total de registros em recebimentos: $countRecebimentos\n\n";

    if ($countRecebimentos > 0) {
        echo "=== PRIMEIROS 5 REGISTROS DA TABELA RECEBIMENTOS ===\n";
        $recebimentos = DB::table('recebimentos')->limit(5)->get();

        foreach ($recebimentos as $recebimento) {
            echo "ID: {$recebimento->id} - Valor: R$ {$recebimento->valor} - Data: {$recebimento->data_recebimento}\n";
        }
    }

    echo "\n=== VERIFICANDO DADOS NA TABELA PAGAMENTOS ===\n";

    // Contar registros na tabela pagamentos
    $countPagamentos = DB::table('pagamentos')->count();
    echo "Total de registros em pagamentos: $countPagamentos\n\n";

    if ($countPagamentos > 0) {
        echo "=== ESTRUTURA DA TABELA PAGAMENTOS ===\n";
        $columns = DB::getSchemaBuilder()->getColumnListing('pagamentos');
        foreach ($columns as $column) {
            echo "- $column\n";
        }

        echo "\n=== VERIFICANDO SE EXISTE CAMPO tipo_id ===\n";
        $hasTipoId = in_array('tipo_id', $columns);
        echo "Campo tipo_id existe: " . ($hasTipoId ? 'SIM' : 'NÃO') . "\n";

        if ($hasTipoId) {
            echo "\n=== CONTANDO POR TIPO_ID ===\n";
            $tipos = DB::table('pagamentos')
                ->select('tipo_id', DB::raw('COUNT(*) as total'))
                ->groupBy('tipo_id')
                ->get();

            foreach ($tipos as $tipo) {
                echo "Tipo ID {$tipo->tipo_id}: {$tipo->total} registros\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
