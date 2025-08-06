<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Support\Facades\DB;

echo "<h2>Teste de Relacionamento Laravel</h2>";

try {
    // 1. Verificar conexão com banco
    echo "<h3>1. Conexão com Banco:</h3>";
    $connection = DB::connection()->getPdo();
    echo "✅ Conectado ao banco<br><br>";

    // 2. Verificar tabelas existem
    echo "<h3>2. Verificar Tabelas:</h3>";
    $tables = DB::select("SHOW TABLES");
    $tableNames = array_map(function ($table) {
        return array_values((array)$table)[0];
    }, $tables);

    $requiredTables = ['empresas', 'empresa_usuarios', 'empresa_user_vinculos'];
    foreach ($requiredTables as $table) {
        if (in_array($table, $tableNames)) {
            echo "✅ Tabela '$table' existe<br>";
        } else {
            echo "❌ Tabela '$table' NÃO existe<br>";
        }
    }
    echo "<br>";

    // 3. Verificar dados nas tabelas
    echo "<h3>3. Dados nas Tabelas:</h3>";
    $empresaCount = DB::table('empresas')->count();
    $usuarioCount = DB::table('empresa_usuarios')->count();
    $vinculoCount = DB::table('empresa_user_vinculos')->count();

    echo "Empresas: $empresaCount<br>";
    echo "Usuários: $usuarioCount<br>";
    echo "Vínculos: $vinculoCount<br><br>";

    // 4. Testar modelo Empresa
    echo "<h3>4. Teste do Modelo Empresa:</h3>";
    $empresa = Empresa::first();
    if ($empresa) {
        echo "✅ Primeira empresa encontrada: ID {$empresa->id} - {$empresa->nome_fantasia}<br>";

        // 5. Testar relacionamento
        echo "<h3>5. Teste do Relacionamento:</h3>";

        // Sem eager loading
        $empresa2 = Empresa::find($empresa->id);
        $usuarios = $empresa2->usuariosVinculados;
        echo "Usuários vinculados (sem eager loading): " . $usuarios->count() . "<br>";

        // Com eager loading
        $empresa3 = Empresa::with('usuariosVinculados')->find($empresa->id);
        $usuarios2 = $empresa3->usuariosVinculados;
        echo "Usuários vinculados (com eager loading): " . $usuarios2->count() . "<br>";

        // SQL da consulta
        echo "SQL gerado: " . $empresa3->usuariosVinculados()->toSql() . "<br>";
        echo "Bindings: " . json_encode($empresa3->usuariosVinculados()->getBindings()) . "<br>";

        // 6. Consulta SQL direta
        echo "<h3>6. Consulta SQL Direta:</h3>";
        $directQuery = DB::select("
            SELECT eu.*, euv.perfil, euv.status, euv.data_vinculo 
            FROM empresa_usuarios eu 
            INNER JOIN empresa_user_vinculos euv ON eu.id = euv.user_id 
            WHERE euv.empresa_id = ?
        ", [$empresa->id]);

        echo "Resultados da consulta direta: " . count($directQuery) . "<br>";

        if (count($directQuery) > 0) {
            echo "<pre>";
            foreach ($directQuery as $row) {
                print_r($row);
            }
            echo "</pre>";
        }
    } else {
        echo "❌ Nenhuma empresa encontrada<br>";
    }
} catch (\Exception $e) {
    echo "<div style='color: red;'>";
    echo "<h3>Erro:</h3>";
    echo "<p>Mensagem: " . $e->getMessage() . "</p>";
    echo "<p>Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
