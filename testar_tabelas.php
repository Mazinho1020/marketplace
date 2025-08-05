<?php

/**
 * TESTE SIMPLES DE CRIAÇÃO DE TABELAS
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 TESTE DE CRIAÇÃO DE TABELAS\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    // Testar conexão
    echo "1️⃣ Testando conexão...\n";
    $pdo = DB::connection()->getPdo();
    echo "✅ Conexão estabelecida!\n";
    echo "   Database: " . config('database.connections.mysql.database') . "\n\n";

    // Verificar tabelas existentes
    echo "2️⃣ Verificando tabelas existentes...\n";
    $tabelas = DB::select("SHOW TABLES");
    foreach ($tabelas as $tabela) {
        $tableName = array_values((array)$tabela)[0];
        if (strpos($tableName, 'empresa') !== false || strpos($tableName, 'marca') !== false) {
            echo "   📋 $tableName\n";
        }
    }

    // Verificar se as tabelas do marketplace já existem
    echo "\n3️⃣ Verificando tabelas do marketplace...\n";

    $tabelasMarketplace = ['marcas', 'empresas_marketplace', 'empresa_user_vinculos'];
    $jaExistem = [];

    foreach ($tabelasMarketplace as $tabela) {
        if (Schema::hasTable($tabela)) {
            echo "   ✅ $tabela: JÁ EXISTE\n";
            $jaExistem[] = $tabela;
        } else {
            echo "   ❌ $tabela: NÃO EXISTE\n";
        }
    }

    if (count($jaExistem) === count($tabelasMarketplace)) {
        echo "\n🎉 TODAS AS TABELAS JÁ EXISTEM!\n";
        echo "   Você pode prosseguir com o teste do painel.\n";
    } else {
        echo "\n⚠️ ALGUMAS TABELAS ESTÃO FALTANDO\n";
        echo "   Você precisa criar as tabelas manualmente no HeidiSQL/phpMyAdmin\n";
        echo "   Use o arquivo: criar_tabelas_marketplace.sql\n";
    }

    // Verificar dados existentes (se as tabelas existem)
    if (in_array('marcas', $jaExistem)) {
        echo "\n4️⃣ Verificando dados existentes...\n";
        $marcas = DB::table('marcas')->count();
        $empresas = DB::table('empresas_marketplace')->count();
        $vinculos = DB::table('empresa_user_vinculos')->count();

        echo "   📊 Marcas: $marcas\n";
        echo "   📊 Empresas: $empresas\n";
        echo "   📊 Vínculos: $vinculos\n";
    }
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 42) . "\n";
