<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== CORRIGINDO TODAS AS COLUNAS FALTANTES ===\n\n";

    // 1. Adicionar colunas faltantes na tabela afi_plan_gateways
    echo "1. Corrigindo tabela afi_plan_gateways...\n";

    $gatewaysColumns = [
        'ambiente' => 'ALTER TABLE afi_plan_gateways ADD COLUMN ambiente enum("sandbox","producao") DEFAULT "sandbox" AFTER provedor',
        'credenciais' => 'ALTER TABLE afi_plan_gateways ADD COLUMN credenciais text DEFAULT NULL AFTER ambiente',
        'configuracoes' => 'ALTER TABLE afi_plan_gateways ADD COLUMN configuracoes text DEFAULT NULL AFTER url_webhook'
    ];

    foreach ($gatewaysColumns as $column => $sql) {
        try {
            $exists = DB::select("SHOW COLUMNS FROM afi_plan_gateways LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
        }
    }

    // 2. Adicionar colunas faltantes na tabela fidelidade_cupons
    echo "\n2. Corrigindo tabela fidelidade_cupons...\n";

    $cuponsColumns = [
        'programa_id' => 'ALTER TABLE fidelidade_cupons ADD COLUMN programa_id int(11) DEFAULT NULL AFTER id',
        'codigo' => 'ALTER TABLE fidelidade_cupons ADD COLUMN codigo varchar(50) DEFAULT NULL AFTER programa_id',
        'descricao' => 'ALTER TABLE fidelidade_cupons ADD COLUMN descricao text DEFAULT NULL AFTER codigo',
        'tipo_desconto' => 'ALTER TABLE fidelidade_cupons ADD COLUMN tipo_desconto enum("percentual","valor_fixo") DEFAULT "percentual" AFTER descricao',
        'valor_desconto' => 'ALTER TABLE fidelidade_cupons ADD COLUMN valor_desconto decimal(10,2) DEFAULT NULL AFTER tipo_desconto',
        'data_inicio' => 'ALTER TABLE fidelidade_cupons ADD COLUMN data_inicio date DEFAULT NULL AFTER valor_desconto',
        'data_fim' => 'ALTER TABLE fidelidade_cupons ADD COLUMN data_fim date DEFAULT NULL AFTER data_inicio',
        'ativo' => 'ALTER TABLE fidelidade_cupons ADD COLUMN ativo tinyint(1) DEFAULT 1 AFTER data_fim',
        'updated_at' => 'ALTER TABLE fidelidade_cupons ADD COLUMN updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at'
    ];

    foreach ($cuponsColumns as $column => $sql) {
        try {
            $exists = DB::select("SHOW COLUMNS FROM fidelidade_cupons LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
        }
    }

    // 3. Adicionar colunas faltantes na tabela fidelidade_cashback_regras
    echo "\n3. Corrigindo tabela fidelidade_cashback_regras...\n";

    $cashbackRegrasColumns = [
        'programa_id' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN programa_id int(11) DEFAULT NULL AFTER id',
        'nome' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN nome varchar(255) DEFAULT NULL AFTER programa_id',
        'percentual' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN percentual decimal(5,2) DEFAULT NULL AFTER nome',
        'valor_minimo' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN valor_minimo decimal(10,2) DEFAULT NULL AFTER percentual',
        'valor_maximo' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN valor_maximo decimal(10,2) DEFAULT NULL AFTER valor_minimo',
        'ativo' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN ativo tinyint(1) DEFAULT 1 AFTER valor_maximo',
        'updated_at' => 'ALTER TABLE fidelidade_cashback_regras ADD COLUMN updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at'
    ];

    foreach ($cashbackRegrasColumns as $column => $sql) {
        try {
            $exists = DB::select("SHOW COLUMNS FROM fidelidade_cashback_regras LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
        }
    }

    // 4. Adicionar colunas faltantes na tabela fidelidade_cashback_transacoes
    echo "\n4. Corrigindo tabela fidelidade_cashback_transacoes...\n";

    $cashbackTransacoesColumns = [
        'cliente_id' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN cliente_id int(11) DEFAULT NULL AFTER id',
        'valor_compra' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN valor_compra decimal(10,2) DEFAULT NULL AFTER cliente_id',
        'valor_cashback' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN valor_cashback decimal(10,2) DEFAULT NULL AFTER valor_compra',
        'percentual_aplicado' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN percentual_aplicado decimal(5,2) DEFAULT NULL AFTER valor_cashback',
        'status' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN status enum("pendente","confirmado","cancelado") DEFAULT "pendente" AFTER percentual_aplicado',
        'data_compra' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN data_compra timestamp DEFAULT NULL AFTER status',
        'updated_at' => 'ALTER TABLE fidelidade_cashback_transacoes ADD COLUMN updated_at timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at'
    ];

    foreach ($cashbackTransacoesColumns as $column => $sql) {
        try {
            $exists = DB::select("SHOW COLUMNS FROM fidelidade_cashback_transacoes LIKE '{$column}'");
            if (empty($exists)) {
                DB::statement($sql);
                echo "  ✅ Coluna {$column} adicionada\n";
            } else {
                echo "  ℹ️  Coluna {$column} já existe\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao adicionar {$column}: " . $e->getMessage() . "\n";
        }
    }

    // 5. Verificar estruturas finais
    echo "\n=== VERIFICAÇÃO FINAL ===\n";

    $tables = ['afi_plan_gateways', 'fidelidade_cupons', 'fidelidade_cashback_regras', 'fidelidade_cashback_transacoes'];

    foreach ($tables as $table) {
        echo "\nEstrutura final {$table}:\n";
        try {
            $structure = DB::select("DESCRIBE {$table}");
            foreach ($structure as $col) {
                echo "  - {$col->Field} ({$col->Type})\n";
            }
        } catch (Exception $e) {
            echo "  ❌ Erro ao verificar {$table}: " . $e->getMessage() . "\n";
        }
    }

    echo "\n🎉 Correção de todas as colunas concluída!\n";
    echo "💡 Agora teste novamente as páginas de fidelidade e pagamentos.\n";
} catch (Exception $e) {
    echo "💥 Erro geral: " . $e->getMessage() . "\n";
}
