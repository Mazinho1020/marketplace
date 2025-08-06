<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSTICO DE PERMISSAO: empresa.visualizar ===\n";

$usuarioId = 7; // mazinho1@gmail.com
$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);

echo "👤 Usuário: {$usuario->nome} ({$usuario->email})\n";
echo "🏢 Empresa ID: {$usuario->empresa_id}\n\n";

// 1. Verificar se a permissão 'empresa.visualizar' existe
echo "=== 1. VERIFICANDO SE A PERMISSAO EXISTE ===\n";
$permissaoEmpresaVisualizar = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'empresa.visualizar')
    ->first();

if ($permissaoEmpresaVisualizar) {
    echo "✅ Permissão 'empresa.visualizar' existe!\n";
    echo "   ID: {$permissaoEmpresaVisualizar->id}\n";
    echo "   Nome: {$permissaoEmpresaVisualizar->nome}\n";
    echo "   Código: {$permissaoEmpresaVisualizar->codigo}\n";
} else {
    echo "❌ Permissão 'empresa.visualizar' NÃO EXISTE!\n";

    // Verificar permissões similares
    echo "\n🔍 Procurando permissões similares com 'empresa':\n";
    $permissoesSimilares = Illuminate\Support\Facades\DB::table('empresa_permissoes')
        ->where('codigo', 'LIKE', '%empresa%')
        ->orWhere('nome', 'LIKE', '%empresa%')
        ->get();

    foreach ($permissoesSimilares as $p) {
        echo "   • ID: {$p->id} | Código: {$p->codigo} | Nome: {$p->nome}\n";
    }
}

// 2. Verificar se o usuário tem essa permissão
echo "\n=== 2. VERIFICANDO SE USUARIO TEM A PERMISSAO ===\n";
if ($permissaoEmpresaVisualizar) {
    $usuarioTemPermissao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
        ->where('usuario_id', $usuarioId)
        ->where('permissao_id', $permissaoEmpresaVisualizar->id)
        ->where('empresa_id', $usuario->empresa_id)
        ->where('is_concedida', 1)
        ->first();

    if ($usuarioTemPermissao) {
        echo "✅ Usuário TEM a permissão empresa.visualizar\n";
        echo "   Concedida: " . ($usuarioTemPermissao->is_concedida ? 'SIM' : 'NÃO') . "\n";
        echo "   Data: {$usuarioTemPermissao->created_at}\n";
    } else {
        echo "❌ Usuário NÃO TEM a permissão empresa.visualizar\n";
    }
}

// 3. Testar com PermissionService
echo "\n=== 3. TESTANDO COM PERMISSION SERVICE ===\n";
$permissionService = new App\Services\Permission\PermissionService();
$temPermissao = $permissionService->hasPermission($usuario, 'empresa.visualizar');
echo ($temPermissao ? "✅" : "❌") . " PermissionService.hasPermission('empresa.visualizar'): " . ($temPermissao ? 'TRUE' : 'FALSE') . "\n";

// 4. Verificar rota específica que está falhando
echo "\n=== 4. ANALISANDO ROTA QUE ESTA FALHANDO ===\n";
echo "Rota testada: /comerciantes/empresas/1/usuarios\n";
echo "Deve corresponder a: usuarios.visualizar OU usuarios.listar\n";

$permissoesRelacionadas = ['usuarios.visualizar', 'usuarios.listar', 'usuarios.index'];
foreach ($permissoesRelacionadas as $perm) {
    $tem = $permissionService->hasPermission($usuario, $perm);
    echo ($tem ? "✅" : "❌") . " {$perm}: " . ($tem ? 'TRUE' : 'FALSE') . "\n";
}
