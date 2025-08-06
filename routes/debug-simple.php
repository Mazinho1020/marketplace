<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/debug/usuarios-simple', function () {
    echo "<h1>DEBUG SIMPLES - Usuários</h1>";

    try {
        // 1. Testar conexão básica
        echo "<h3>1. Testando conexão com banco</h3>";
        $databases = DB::select('SHOW DATABASES');
        echo "Conexão OK. Bancos disponíveis: " . count($databases) . "<br>";

        // 2. Verificar banco atual
        $currentDb = DB::select('SELECT DATABASE() as db')[0]->db;
        echo "Banco atual: {$currentDb}<br>";

        // 3. Verificar tabelas
        echo "<h3>2. Verificando tabelas</h3>";
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(function ($table) use ($currentDb) {
            return $table->{"Tables_in_{$currentDb}"};
        }, $tables);

        echo "Tabelas encontradas:<br>";
        foreach ($tableNames as $table) {
            echo "- {$table}<br>";
        }

        // 4. Verificar especificamente as tabelas que precisamos
        $requiredTables = ['empresas', 'empresa_usuarios', 'empresa_user_vinculos'];
        echo "<h3>3. Verificando tabelas necessárias</h3>";

        foreach ($requiredTables as $table) {
            if (in_array($table, $tableNames)) {
                $count = DB::table($table)->count();
                echo "✅ {$table}: {$count} registros<br>";
            } else {
                echo "❌ {$table}: não encontrada<br>";
            }
        }

        // 5. Dados específicos da empresa 2
        echo "<h3>4. Dados da empresa ID 2</h3>";

        $empresa = DB::table('empresas')->where('id', 2)->first();
        if ($empresa) {
            echo "✅ Empresa encontrada: {$empresa->nome_fantasia}<br>";

            $vinculos = DB::table('empresa_user_vinculos')->where('empresa_id', 2)->get();
            echo "Vínculos para empresa 2: {$vinculos->count()}<br>";

            foreach ($vinculos as $vinculo) {
                $usuario = DB::table('empresa_usuarios')->where('id', $vinculo->user_id)->first();
                echo "- User ID {$vinculo->user_id}: " . ($usuario->nome ?? 'NOME NULL') . " ({$vinculo->perfil})<br>";
            }
        } else {
            echo "❌ Empresa ID 2 não encontrada<br>";
        }
    } catch (Exception $e) {
        echo "❌ ERRO: " . $e->getMessage() . "<br>";
        echo "Linha: " . $e->getLine() . " Arquivo: " . $e->getFile() . "<br>";
    }

    echo "<br><br><a href='/comerciantes/empresas/2/usuarios'>← Voltar para página de usuários</a>";
});
