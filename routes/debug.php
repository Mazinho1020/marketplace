<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Comerciantes\Models\Empresa;
use App\Comerciantes\Models\EmpresaUsuario;

Route::get('/debug/usuarios', function () {
    try {
        echo "<h1>DEBUG: Relacionamento Usuários</h1>";

        // 1. Verificar se a tabela existe
        echo "<h3>1. Verificando tabela empresa_user_vinculos</h3>";
        $vinculos = DB::table('empresa_user_vinculos')->get();
        echo "Total de vínculos na tabela: " . $vinculos->count() . "<br>";

        if ($vinculos->count() > 0) {
            echo "<h4>Dados na tabela:</h4>";
            foreach ($vinculos as $vinculo) {
                echo "ID: {$vinculo->id}, Empresa: {$vinculo->empresa_id}, User: {$vinculo->user_id}, Perfil: {$vinculo->perfil}<br>";
            }
        }

        // 2. Testar empresa específica (ID 2 do seu SQL)
        echo "<h3>2. Testando empresa ID 2</h3>";
        $empresa = Empresa::find(2);

        if ($empresa) {
            echo "Empresa encontrada: {$empresa->nome_fantasia}<br>";

            // Carregar usuários vinculados
            $empresa->load('usuariosVinculados');
            echo "Usuários vinculados (count): " . $empresa->usuariosVinculados->count() . "<br>";

            if ($empresa->usuariosVinculados->count() > 0) {
                echo "<h4>Usuários encontrados:</h4>";
                foreach ($empresa->usuariosVinculados as $usuario) {
                    echo "- Nome: " . ($usuario->nome ?? 'SEM NOME') .
                        ", Email: " . ($usuario->email ?? 'SEM EMAIL') .
                        ", Perfil: " . ($usuario->pivot->perfil ?? 'SEM PERFIL') . "<br>";
                }
            } else {
                echo "❌ Nenhum usuário vinculado encontrado!<br>";

                // Debug da query
                echo "<h4>Debug da Query:</h4>";
                $query = $empresa->usuariosVinculados()->toSql();
                echo "SQL: " . $query . "<br>";

                // Verificar se o usuário existe
                $usuario = EmpresaUsuario::find(3);
                if ($usuario) {
                    echo "Usuário ID 3 existe: {$usuario->nome} ({$usuario->email})<br>";
                } else {
                    echo "❌ Usuário ID 3 não encontrado!<br>";
                }
            }
        } else {
            echo "❌ Empresa ID 2 não encontrada!<br>";

            // Listar todas as empresas
            $empresas = Empresa::all();
            echo "Empresas disponíveis:<br>";
            foreach ($empresas as $emp) {
                echo "- ID: {$emp->id}, Nome: {$emp->nome_fantasia}<br>";
            }
        }

        // 3. Testar query direta
        echo "<h3>3. Query direta com JOIN</h3>";
        $resultado = DB::table('empresa_user_vinculos as euv')
            ->join('empresa_usuarios as eu', 'euv.user_id', '=', 'eu.id')
            ->join('empresas as e', 'euv.empresa_id', '=', 'e.id')
            ->where('euv.empresa_id', 2)
            ->select('eu.nome', 'eu.email', 'euv.perfil', 'e.nome_fantasia')
            ->get();

        echo "Resultado do JOIN: " . $resultado->count() . " registros<br>";
        foreach ($resultado as $row) {
            echo "- {$row->nome} ({$row->email}) na empresa {$row->nome_fantasia}<br>";
        }
    } catch (Exception $e) {
        echo "<h3>❌ ERRO:</h3>";
        echo "Mensagem: " . $e->getMessage() . "<br>";
        echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "<br>";
    }
});
