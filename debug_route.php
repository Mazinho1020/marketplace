<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;

Route::get('/debug-specific', function () {
    $output = "<h2>Debug Específico do Problema</h2>";

    try {
        // 1. Verificar dados básicos
        $output .= "<h3>1. Dados Básicos:</h3>";
        $empresaCount = DB::table('empresas')->count();
        $usuarioCount = DB::table('empresa_usuarios')->count();
        $vinculoCount = DB::table('empresa_user_vinculos')->count();

        $output .= "Empresas: $empresaCount<br>";
        $output .= "Usuários: $usuarioCount<br>";
        $output .= "Vínculos: $vinculoCount<br><br>";

        // 2. Buscar primeira empresa
        $output .= "<h3>2. Primeira Empresa:</h3>";
        $empresa = Empresa::first();

        if (!$empresa) {
            $output .= "❌ Nenhuma empresa encontrada no banco!<br>";
            return $output;
        }

        $output .= "✅ Empresa encontrada: ID {$empresa->id} - {$empresa->nome_fantasia}<br><br>";

        // 3. Testar consulta SQL direta
        $output .= "<h3>3. Consulta SQL Direta:</h3>";
        $directData = DB::select("
            SELECT eu.id, eu.nome, eu.email, euv.perfil, euv.status
            FROM empresa_usuarios eu 
            INNER JOIN empresa_user_vinculos euv ON eu.id = euv.user_id 
            WHERE euv.empresa_id = ?
        ", [$empresa->id]);

        $output .= "Registros encontrados na consulta direta: " . count($directData) . "<br>";

        if (count($directData) > 0) {
            $output .= "<table border='1' cellpadding='5'>";
            $output .= "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Perfil</th><th>Status</th></tr>";
            foreach ($directData as $row) {
                $output .= "<tr>";
                $output .= "<td>{$row->id}</td>";
                $output .= "<td>{$row->nome}</td>";
                $output .= "<td>{$row->email}</td>";
                $output .= "<td>{$row->perfil}</td>";
                $output .= "<td>{$row->status}</td>";
                $output .= "</tr>";
            }
            $output .= "</table><br>";
        }

        // 4. Testar relacionamento Laravel
        $output .= "<h3>4. Relacionamento Laravel:</h3>";

        // Recarregar empresa com relacionamento
        $empresaComRelacao = Empresa::with(['usuariosVinculados' => function ($query) {
            $query->withPivot(['perfil', 'status', 'permissoes', 'data_vinculo']);
        }])->find($empresa->id);

        $usuarios = $empresaComRelacao->usuariosVinculados;
        $output .= "Usuários retornados pelo relacionamento Laravel: " . $usuarios->count() . "<br>";

        // 5. Debug da query Laravel
        $output .= "<h3>5. Query Laravel:</h3>";
        $query = $empresaComRelacao->usuariosVinculados();
        $output .= "SQL: " . $query->toSql() . "<br>";
        $output .= "Bindings: " . json_encode($query->getBindings()) . "<br><br>";

        // 6. Verificar estrutura da tabela pivot
        $output .= "<h3>6. Estrutura da Tabela Pivot:</h3>";
        $columns = DB::select("DESCRIBE empresa_user_vinculos");
        $output .= "<table border='1' cellpadding='5'>";
        $output .= "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            $output .= "<tr>";
            $output .= "<td>{$column->Field}</td>";
            $output .= "<td>{$column->Type}</td>";
            $output .= "<td>{$column->Null}</td>";
            $output .= "<td>{$column->Key}</td>";
            $output .= "<td>{$column->Default}</td>";
            $output .= "</tr>";
        }
        $output .= "</table><br>";

        // 7. Dados brutos da tabela pivot
        $output .= "<h3>7. Dados da Tabela Pivot:</h3>";
        $pivotData = DB::select("SELECT * FROM empresa_user_vinculos WHERE empresa_id = ?", [$empresa->id]);
        $output .= "Registros na tabela pivot: " . count($pivotData) . "<br>";

        if (count($pivotData) > 0) {
            $output .= "<table border='1' cellpadding='5'>";
            $output .= "<tr><th>empresa_id</th><th>user_id</th><th>perfil</th><th>status</th><th>data_vinculo</th></tr>";
            foreach ($pivotData as $row) {
                $output .= "<tr>";
                $output .= "<td>{$row->empresa_id}</td>";
                $output .= "<td>{$row->user_id}</td>";
                $output .= "<td>{$row->perfil}</td>";
                $output .= "<td>{$row->status}</td>";
                $output .= "<td>{$row->data_vinculo}</td>";
                $output .= "</tr>";
            }
            $output .= "</table><br>";
        }
    } catch (\Exception $e) {
        $output .= "<div style='color: red;'>";
        $output .= "<h3>Erro:</h3>";
        $output .= "<p>Mensagem: " . $e->getMessage() . "</p>";
        $output .= "<p>Arquivo: " . $e->getFile() . " (linha " . $e->getLine() . ")</p>";
        $output .= "<pre>" . $e->getTraceAsString() . "</pre>";
        $output .= "</div>";
    }

    return $output;
});
