<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE RAPIDO DE PERMISSOES (SEM CACHE) ===\n";

$usuarioId = 3; // mazinho1@gmail.com
$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

echo "👤 Usuário: {$usuario->nome} ({$usuario->email})\n\n";

// Testar algumas permissões
$permissoesTeste = [
    'dashboard.visualizar',
    'empresas.visualizar',
    'usuarios.visualizar'
];

foreach ($permissoesTeste as $perm) {
    $tem = $permissionService->hasPermission($usuario, $perm);
    echo ($tem ? "✅" : "❌") . " {$perm}: " . ($tem ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n=== FAZENDO LOGIN E TESTANDO ACESSO ===\n";

// Fazer login
Illuminate\Support\Facades\Auth::guard('comerciante')->login($usuario);

if (Illuminate\Support\Facades\Auth::guard('comerciante')->check()) {
    echo "✅ Login realizado com sucesso!\n";
    echo "🆔 Usuário logado ID: " . Illuminate\Support\Facades\Auth::guard('comerciante')->id() . "\n";

    echo "\n🔗 TESTE ESTES LINKS AGORA:\n";
    echo "📊 Dashboard: http://localhost:8000/comerciantes/dashboard\n";
    echo "👥 Usuários: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
} else {
    echo "❌ Falha no login!\n";
}
