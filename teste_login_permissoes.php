<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LOGIN AUTOMATICO E TESTE DE PERMISSOES ===\n";

// Fazer login com o usuário que tem todas as permissões
$usuarioId = 7; // mazinho1@gmail.com
$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);

if (!$usuario) {
    echo "❌ Usuário não encontrado!\n";
    exit(1);
}

echo "👤 Fazendo login como: {$usuario->nome} ({$usuario->email})\n";

// Simular login
Illuminate\Support\Facades\Auth::guard('comerciante')->login($usuario);

if (Illuminate\Support\Facades\Auth::guard('comerciante')->check()) {
    echo "✅ Login realizado com sucesso!\n";
    echo "🆔 Usuário logado ID: " . Illuminate\Support\Facades\Auth::guard('comerciante')->id() . "\n";
    echo "🏢 Empresa ID: {$usuario->empresa_id}\n\n";

    // Testar o PermissionService
    echo "=== TESTANDO PERMISSION SERVICE ===\n";

    $permissionService = new App\Services\Permission\PermissionService();

    // Testar algumas permissões específicas que sabemos que existem
    $permissoesTeste = [
        'dashboard.visualizar',
        'usuarios.listar',
        'usuarios.criar',
        'usuarios.editar',
        'usuarios.excluir'
    ];

    foreach ($permissoesTeste as $permissao) {
        $temPermissao = $permissionService->hasPermission($usuario, $permissao);
        echo ($temPermissao ? "✅" : "❌") . " {$permissao}\n";
    }
} else {
    echo "❌ Falha no login!\n";
}
