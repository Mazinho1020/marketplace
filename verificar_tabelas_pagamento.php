<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICANDO TABELAS DE PAGAMENTO ===\n";

$tables = ['formas_pagamento', 'forma_pag_bandeiras'];

foreach ($tables as $tableName) {
    try {
        $result = DB::select("SHOW TABLES LIKE '$tableName'");
        if (!empty($result)) {
            echo "\n✅ TABELA: $tableName\n";

            // Mostrar estrutura da tabela
            $columns = DB::select("DESCRIBE $tableName");
            echo "   Colunas:\n";
            foreach ($columns as $col) {
                echo "     - {$col->Field} ({$col->Type})\n";
            }

            // Contar registros
            $count = DB::table($tableName)->count();
            echo "   Total de registros: $count\n";

            // Mostrar alguns registros
            if ($count > 0) {
                $records = DB::table($tableName)->limit(3)->get();
                echo "   Primeiros registros:\n";
                foreach ($records as $record) {
                    echo "     ID {$record->id}: ";
                    if (isset($record->nome)) {
                        echo $record->nome;
                    }
                    if (isset($record->tipo)) {
                        echo " (tipo: {$record->tipo})";
                    }
                    echo "\n";
                }
            }
        } else {
            echo "❌ NÃO EXISTE: $tableName\n";
        }
    } catch (Exception $e) {
        echo "❌ ERRO ao verificar $tableName: " . $e->getMessage() . "\n";
    }
}
