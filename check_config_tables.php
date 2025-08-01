<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Verificando tabelas de configuração...\n\n";

    // Verificar tabelas de config
    $tables = ['config_groups', 'config_sites', 'config_environments', 'config_definitions', 'config_values'];

    foreach ($tables as $table) {
        try {
            $exists = DB::select("SHOW TABLES LIKE '$table'");
            if (!empty($exists)) {
                $count = DB::table($table)->count();
                echo "✓ Tabela $table existe - $count registros\n";
            } else {
                echo "✗ Tabela $table NÃO existe\n";
            }
        } catch (Exception $e) {
            echo "✗ Erro ao verificar tabela $table: " . $e->getMessage() . "\n";
        }
    }

    echo "\n";

    // Se todas as tabelas existem, verificar dados nos grupos
    if (DB::select("SHOW TABLES LIKE 'config_groups'")) {
        $groups = DB::table('config_groups')->get();
        echo "Grupos encontrados:\n";
        foreach ($groups as $group) {
            echo "- ID: {$group->id}, Código: {$group->codigo}, Nome: {$group->nome}\n";
        }
        echo "\n";
    }

    // Verificar dados nos sites
    if (DB::select("SHOW TABLES LIKE 'config_sites'")) {
        $sites = DB::table('config_sites')->get();
        echo "Sites encontrados:\n";
        foreach ($sites as $site) {
            echo "- ID: {$site->id}, Código: {$site->codigo}, Nome: {$site->nome}\n";
        }
        echo "\n";
    }

    // Verificar dados nos ambientes
    if (DB::select("SHOW TABLES LIKE 'config_environments'")) {
        $environments = DB::table('config_environments')->get();
        echo "Ambientes encontrados:\n";
        foreach ($environments as $env) {
            echo "- ID: {$env->id}, Código: {$env->codigo}, Nome: {$env->nome}\n";
        }
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
