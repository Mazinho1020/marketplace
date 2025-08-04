<?php

require_once 'vendor/autoload.php';

// Carregar configuração do Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Verificando tabelas existentes no banco...\n\n";

    $tables = DB::select('SHOW TABLES');
    $existingTables = [];

    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $existingTables[] = $tableName;
        echo "- " . $tableName . "\n";
    }

    echo "\nTotal de tabelas: " . count($existingTables) . "\n\n";

    // Tabelas do arquivo SQL
    $sqlTables = [
        'afi_plan_assinaturas',
        'afi_plan_configuracoes',
        'afi_plan_gateways',
        'afi_plan_planos',
        'afi_plan_transacoes',
        'afi_plan_vendas',
        'caixas',
        'caixa_fechamento',
        'caixa_fechamento_formas',
        'caixa_movimentos',
        'categorias_sugeridas',
        'classificacoes_dre',
        'clientes',
        'com_planos',
        'config',
        'config_definitions'
    ];

    echo "Tabelas que precisam ser criadas:\n";
    $needsCreation = [];

    foreach ($sqlTables as $table) {
        if (!in_array($table, $existingTables)) {
            $needsCreation[] = $table;
            echo "- " . $table . "\n";
        }
    }

    if (empty($needsCreation)) {
        echo "Todas as tabelas já existem!\n";
    } else {
        echo "\nTotal de tabelas para criar: " . count($needsCreation) . "\n";
        echo "\nCriando tabelas faltantes...\n";

        // Criar tabelas básicas
        $created = 0;

        foreach ($needsCreation as $table) {
            try {
                switch ($table) {
                    case 'afi_plan_gateways':
                        DB::statement("CREATE TABLE IF NOT EXISTS afi_plan_gateways (
                            id int(11) NOT NULL AUTO_INCREMENT,
                            empresa_id int(11) NOT NULL,
                            codigo varchar(50) NOT NULL,
                            nome varchar(100) NOT NULL,
                            ativo tinyint(1) DEFAULT 1,
                            created_at timestamp NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
                        break;

                    case 'afi_plan_planos':
                        DB::statement("CREATE TABLE IF NOT EXISTS afi_plan_planos (
                            id int(11) NOT NULL AUTO_INCREMENT,
                            empresa_id int(11) NOT NULL,
                            codigo varchar(50) NOT NULL,
                            nome varchar(100) NOT NULL,
                            preco_mensal decimal(10,2) DEFAULT 0.00,
                            ativo tinyint(1) DEFAULT 1,
                            created_at timestamp NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
                        break;

                    case 'clientes':
                        DB::statement("CREATE TABLE IF NOT EXISTS clientes (
                            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                            nome varchar(255) DEFAULT NULL,
                            email varchar(255) DEFAULT NULL,
                            empresa_id int(11) DEFAULT NULL,
                            created_at timestamp NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                        break;

                    case 'config':
                        DB::statement("CREATE TABLE IF NOT EXISTS config (
                            id int(11) NOT NULL AUTO_INCREMENT,
                            name varchar(255) NOT NULL,
                            value text DEFAULT NULL,
                            empresa_id int(11) DEFAULT NULL,
                            created_at timestamp NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
                        break;

                    default:
                        // Criar tabela básica genérica
                        DB::statement("CREATE TABLE IF NOT EXISTS {$table} (
                            id int(11) NOT NULL AUTO_INCREMENT,
                            empresa_id int(11) DEFAULT NULL,
                            created_at timestamp NOT NULL DEFAULT current_timestamp(),
                            PRIMARY KEY (id)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
                        break;
                }

                echo "✅ Criada: {$table}\n";
                $created++;
            } catch (Exception $e) {
                echo "❌ Erro {$table}: " . $e->getMessage() . "\n";
            }
        }

        echo "\n=== RESUMO ===\n";
        echo "Tabelas criadas: {$created}\n";
        echo "🎉 Processo concluído!\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
