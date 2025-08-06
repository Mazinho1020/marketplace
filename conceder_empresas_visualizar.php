<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CONCEDENDO PERMISSAO empresas.visualizar ===\n";

$usuarioId = 7;
$empresaId = 1;

// Buscar a permissão empresas.visualizar
$permissao = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'empresas.visualizar')
    ->first();

if (!$permissao) {
    echo "❌ Permissão empresas.visualizar não encontrada!\n";
    exit(1);
}

echo "✅ Permissão encontrada: {$permissao->nome} (ID: {$permissao->id})\n";

// Verificar se o usuário já tem essa permissão
$jaTemPermissao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
    ->where('usuario_id', $usuarioId)
    ->where('permissao_id', $permissao->id)
    ->where('empresa_id', $empresaId)
    ->first();

if ($jaTemPermissao) {
    echo "ℹ️  Usuário já tem essa permissão. Atualizando para concedida...\n";

    Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
        ->where('usuario_id', $usuarioId)
        ->where('permissao_id', $permissao->id)
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
        'permissao_id' => $permissao->id,
        'is_concedida' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
}

// Testar com PermissionService
echo "\n=== TESTANDO COM PERMISSION SERVICE ===\n";
$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

$permissoesTeste = [
    'empresas.visualizar',
    'empresas.listar',
    'usuarios.visualizar',
    'usuarios.listar'
];

foreach ($permissoesTeste as $perm) {
    $tem = $permissionService->hasPermission($usuario, $perm);
    echo ($tem ? "✅" : "❌") . " {$perm}: " . ($tem ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n🎉 Processo concluído! Teste acessando: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
