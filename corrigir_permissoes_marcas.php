<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSTICO E CORREÇÃO: marcas.visualizar ===\n";

$usuarioId = 3; // mazinho1@gmail.com
$empresaId = 1;

// 1. Verificar se a permissão existe
echo "=== 1. VERIFICANDO SE A PERMISSAO EXISTE ===\n";
$permissaoMarcas = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'marcas.visualizar')
    ->first();

if ($permissaoMarcas) {
    echo "✅ Permissão 'marcas.visualizar' existe!\n";
    echo "   ID: {$permissaoMarcas->id}\n";
    echo "   Nome: {$permissaoMarcas->nome}\n";
} else {
    echo "❌ Permissão 'marcas.visualizar' NÃO EXISTE!\n";

    // Procurar permissões relacionadas a marcas
    echo "\n🔍 Procurando permissões com 'marca':\n";
    $permissoesMarcas = Illuminate\Support\Facades\DB::table('empresa_permissoes')
        ->where('codigo', 'LIKE', '%marca%')
        ->orWhere('nome', 'LIKE', '%marca%')
        ->get();

    if ($permissoesMarcas->count() > 0) {
        foreach ($permissoesMarcas as $p) {
            echo "   • ID: {$p->id} | Código: {$p->codigo} | Nome: {$p->nome}\n";
        }
    } else {
        echo "   ❌ Nenhuma permissão relacionada a marcas encontrada\n";

        echo "\n➕ CRIANDO PERMISSOES DE MARCAS...\n";

        // Criar as permissões básicas para marcas
        $permissoesPadrao = [
            ['codigo' => 'marcas.visualizar', 'nome' => 'Ver Marcas', 'descricao' => 'Visualizar detalhes de marcas'],
            ['codigo' => 'marcas.listar', 'nome' => 'Listar Marcas', 'descricao' => 'Ver lista de marcas'],
            ['codigo' => 'marcas.criar', 'nome' => 'Criar Marcas', 'descricao' => 'Criar novas marcas'],
            ['codigo' => 'marcas.editar', 'nome' => 'Editar Marcas', 'descricao' => 'Editar marcas existentes'],
            ['codigo' => 'marcas.excluir', 'nome' => 'Excluir Marcas', 'descricao' => 'Excluir marcas'],
        ];

        foreach ($permissoesPadrao as $perm) {
            $id = Illuminate\Support\Facades\DB::table('empresa_permissoes')->insertGetId([
                'codigo' => $perm['codigo'],
                'nome' => $perm['nome'],
                'descricao' => $perm['descricao'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✅ Criada: {$perm['codigo']} (ID: {$id})\n";
        }

        // Atualizar a variável para usar a permissão recém-criada
        $permissaoMarcas = Illuminate\Support\Facades\DB::table('empresa_permissoes')
            ->where('codigo', 'marcas.visualizar')
            ->first();
    }
}

// 2. Conceder a permissão ao usuário
if ($permissaoMarcas) {
    echo "\n=== 2. CONCEDENDO PERMISSAO AO USUARIO ===\n";

    $jaTemPermissao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
        ->where('usuario_id', $usuarioId)
        ->where('permissao_id', $permissaoMarcas->id)
        ->where('empresa_id', $empresaId)
        ->first();

    if ($jaTemPermissao) {
        echo "ℹ️  Usuário já tem essa permissão. Atualizando...\n";
        Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
            ->where('usuario_id', $usuarioId)
            ->where('permissao_id', $permissaoMarcas->id)
            ->where('empresa_id', $empresaId)
            ->update([
                'is_concedida' => 1,
                'updated_at' => now()
            ]);
    } else {
        echo "➕ Concedendo permissão ao usuário...\n";
        Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insert([
            'usuario_id' => $usuarioId,
            'empresa_id' => $empresaId,
            'permissao_id' => $permissaoMarcas->id,
            'is_concedida' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    echo "✅ Permissão concedida!\n";
}

// 3. Conceder todas as permissões de marcas criadas
echo "\n=== 3. CONCEDENDO TODAS AS PERMISSOES DE MARCAS ===\n";
$todasPermissoesMarcas = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'LIKE', 'marcas.%')
    ->get();

foreach ($todasPermissoesMarcas as $perm) {
    $jaExiste = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
        ->where('usuario_id', $usuarioId)
        ->where('permissao_id', $perm->id)
        ->where('empresa_id', $empresaId)
        ->exists();

    if (!$jaExiste) {
        Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insert([
            'usuario_id' => $usuarioId,
            'empresa_id' => $empresaId,
            'permissao_id' => $perm->id,
            'is_concedida' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ {$perm->codigo}\n";
    }
}

// 4. Limpar cache e testar
echo "\n=== 4. LIMPANDO CACHE E TESTANDO ===\n";
Illuminate\Support\Facades\Cache::flush();

$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

$permissoesTeste = ['marcas.visualizar', 'marcas.listar', 'marcas.criar', 'marcas.editar'];
foreach ($permissoesTeste as $perm) {
    $tem = $permissionService->hasPermission($usuario, $perm);
    echo ($tem ? "✅" : "❌") . " {$perm}: " . ($tem ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n🎉 PROCESSO CONCLUÍDO!\n";
echo "🔗 Agora teste: http://localhost:8000/comerciantes/marcas\n";
