<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar o Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📊 ESTRUTURA COMPLETA DO BANCO DE DADOS\n";
echo "======================================\n\n";

try {

    // Listar todas as tabelas
    echo "🗂️ LISTANDO TODAS AS TABELAS:\n";
    echo str_repeat("-", 50) . "\n";

    $tables = DB::select('SHOW TABLES');
    $tablesList = [];

    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $tablesList[] = $tableName;
    }

    sort($tablesList);

    echo "Total de tabelas: " . count($tablesList) . "\n\n";

    // Agrupar tabelas por categoria
    $categorias = [
        'Financeiro' => ['lancamentos', 'pagamentos', 'recebimentos', 'forma_pag', 'conta_', 'categoria_'],
        'Usuários' => ['users', 'pessoa', 'cliente', 'funcionario'],
        'Empresas' => ['empresa', 'marca'],
        'Produtos' => ['produto', 'categoria', 'estoque', 'variacao'],
        'Pedidos' => ['pedido', 'item_pedido', 'carrinho'],
        'Notificações' => ['notificacao', 'notif_'],
        'Sistema' => ['migration', 'session', 'cache', 'job'],
        'Configurações' => ['config', 'permiss', 'role']
    ];

    foreach ($categorias as $categoria => $patterns) {
        echo "📁 {$categoria}:\n";

        $found = false;
        foreach ($tablesList as $table) {
            foreach ($patterns as $pattern) {
                if (stripos($table, $pattern) !== false) {
                    echo "   • {$table}\n";
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            echo "   (nenhuma tabela encontrada)\n";
        }
        echo "\n";
    }

    // Outras tabelas
    echo "📁 Outras Tabelas:\n";
    $categorizadas = [];
    foreach ($categorias as $patterns) {
        foreach ($tablesList as $table) {
            foreach ($patterns as $pattern) {
                if (stripos($table, $pattern) !== false) {
                    $categorizadas[] = $table;
                    break 2;
                }
            }
        }
    }

    $outras = array_diff($tablesList, $categorizadas);
    if (count($outras) > 0) {
        foreach ($outras as $table) {
            echo "   • {$table}\n";
        }
    } else {
        echo "   (todas as tabelas foram categorizadas)\n";
    }

    echo "\n" . str_repeat("=", 50) . "\n\n";

    // Focar nas tabelas financeiras
    echo "💰 DETALHES DAS TABELAS FINANCEIRAS:\n";
    echo str_repeat("-", 50) . "\n\n";

    $tabelasFinanceiras = [];
    foreach ($tablesList as $table) {
        if (
            stripos($table, 'lancamento') !== false ||
            stripos($table, 'pagamento') !== false ||
            stripos($table, 'recebimento') !== false ||
            stripos($table, 'forma_pag') !== false ||
            stripos($table, 'conta_') !== false ||
            stripos($table, 'categoria_conta') !== false
        ) {
            $tabelasFinanceiras[] = $table;
        }
    }

    foreach ($tabelasFinanceiras as $table) {
        echo "🏦 Tabela: {$table}\n";

        try {
            $columns = DB::select("SHOW COLUMNS FROM `{$table}`");
            foreach ($columns as $column) {
                $null = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = $column->Default ? " DEFAULT '{$column->Default}'" : '';
                echo "   📋 {$column->Field} - {$column->Type} {$null}{$default}\n";
            }

            // Contar registros
            $count = DB::table($table)->count();
            echo "   📊 Total de registros: {$count}\n";
        } catch (Exception $e) {
            echo "   ❌ Erro ao acessar tabela: " . $e->getMessage() . "\n";
        }

        echo "\n" . str_repeat("-", 30) . "\n\n";
    }

    // Estatísticas gerais
    echo "📈 ESTATÍSTICAS GERAIS:\n";
    echo str_repeat("-", 50) . "\n";

    $stats = [
        'Total de tabelas' => count($tablesList),
        'Tabelas financeiras' => count($tabelasFinanceiras),
        'Outras tabelas' => count($tablesList) - count($tabelasFinanceiras)
    ];

    foreach ($stats as $label => $value) {
        echo "   {$label}: {$value}\n";
    }

    echo "\n🎯 FOCO NO SISTEMA DE RECEBIMENTOS:\n";
    echo str_repeat("-", 50) . "\n";

    // Verificar tabelas específicas do sistema de recebimentos
    $tabelasRecebimento = ['recebimentos', 'formas_pagamento', 'forma_pag_bandeiras', 'forma_pagamento_bandeiras'];

    foreach ($tabelasRecebimento as $table) {
        if (in_array($table, $tablesList)) {
            $count = DB::table($table)->count();
            echo "   ✅ {$table}: {$count} registros\n";
        } else {
            echo "   ❌ {$table}: tabela não encontrada\n";
        }
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
