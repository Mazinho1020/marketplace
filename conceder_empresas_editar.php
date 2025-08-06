<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CONCEDENDO PERMISSAO: empresas.editar ===\n";

$usuarioId = 3; // mazinho1@gmail.com
$empresaId = 1;

// 1. Verificar se a permissão existe
$permissaoEmpresasEditar = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'empresas.editar')
    ->first();

if ($permissaoEmpresasEditar) {
    echo "✅ Permissão 'empresas.editar' existe (ID: {$permissaoEmpresasEditar->id})\n";
} else {
    echo "❌ Permissão 'empresas.editar' não encontrada!\n";
    exit(1);
}

// 2. Verificar se o usuário já tem essa permissão
$jaTemPermissao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
    ->where('usuario_id', $usuarioId)
    ->where('permissao_id', $permissaoEmpresasEditar->id)
    ->where('empresa_id', $empresaId)
    ->first();

if ($jaTemPermissao) {
    echo "ℹ️  Usuário já tem essa permissão. Verificando se está concedida...\n";

    if ($jaTemPermissao->is_concedida) {
        echo "✅ Permissão já está concedida!\n";
    } else {
        echo "⚠️  Permissão existe mas não está concedida. Atualizando...\n";
        Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
            ->where('usuario_id', $usuarioId)
            ->where('permissao_id', $permissaoEmpresasEditar->id)
            ->where('empresa_id', $empresaId)
            ->update([
                'is_concedida' => 1,
                'updated_at' => now()
            ]);
        echo "✅ Permissão atualizada para concedida!\n";
    }
} else {
    echo "➕ Concedendo permissão ao usuário...\n";
    Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insert([
        'usuario_id' => $usuarioId,
        'empresa_id' => $empresaId,
        'permissao_id' => $permissaoEmpresasEditar->id,
        'is_concedida' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "✅ Permissão concedida!\n";
}

// 3. Conceder outras permissões de empresas que podem ser necessárias
echo "\n=== VERIFICANDO OUTRAS PERMISSOES DE EMPRESAS ===\n";
$permissoesEmpresas = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'LIKE', 'empresas.%')
    ->get();

echo "Permissões de empresas encontradas:\n";
foreach ($permissoesEmpresas as $perm) {
    $temPermissao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
        ->where('usuario_id', $usuarioId)
        ->where('permissao_id', $perm->id)
        ->where('empresa_id', $empresaId)
        ->where('is_concedida', 1)
        ->exists();

    echo ($temPermissao ? "✅" : "❌") . " {$perm->codigo} - {$perm->nome}\n";

    // Se não tem, conceder
    if (!$temPermissao) {
        $jaExisteRelacao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
            ->where('usuario_id', $usuarioId)
            ->where('permissao_id', $perm->id)
            ->where('empresa_id', $empresaId)
            ->exists();

        if ($jaExisteRelacao) {
            // Atualizar para concedida
            Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
                ->where('usuario_id', $usuarioId)
                ->where('permissao_id', $perm->id)
                ->where('empresa_id', $empresaId)
                ->update(['is_concedida' => 1, 'updated_at' => now()]);
        } else {
            // Inserir nova permissão
            Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insert([
                'usuario_id' => $usuarioId,
                'empresa_id' => $empresaId,
                'permissao_id' => $perm->id,
                'is_concedida' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        echo "   ➕ Permissão {$perm->codigo} concedida!\n";
    }
}

// 4. Limpar cache e testar
echo "\n=== LIMPANDO CACHE E TESTANDO ===\n";
Illuminate\Support\Facades\Cache::flush();

$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

$permissoesTeste = [
    'empresas.visualizar',
    'empresas.editar',
    'empresas.listar',
    'empresas.criar',
    'empresas.excluir'
];

foreach ($permissoesTeste as $perm) {
    $tem = $permissionService->hasPermission($usuario, $perm);
    echo ($tem ? "✅" : "❌") . " {$perm}: " . ($tem ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n🎉 PROCESSO CONCLUÍDO!\n";
echo "📝 Agora você pode editar empresas sem problemas!\n";
