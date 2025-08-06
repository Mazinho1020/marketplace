<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LOGIN AUTOMATICO FINAL ===\n";

$usuarioId = 3; // mazinho1@gmail.com
$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);

echo "👤 Fazendo login como: {$usuario->nome} ({$usuario->email})\n";

// Fazer login
Illuminate\Support\Facades\Auth::guard('comerciante')->login($usuario);

if (Illuminate\Support\Facades\Auth::guard('comerciante')->check()) {
    echo "✅ Login realizado com sucesso!\n";
    echo "🆔 Usuário logado ID: " . Illuminate\Support\Facades\Auth::guard('comerciante')->id() . "\n";
    echo "🏢 Empresa ID: {$usuario->empresa_id}\n\n";

    // Verificar permissões finais
    $permissionService = new App\Services\Permission\PermissionService();
    $permissoesCriticas = [
        'dashboard.visualizar' => 'Dashboard',
        'empresas.visualizar' => 'Empresas',
        'usuarios.visualizar' => 'Usuários',
        'marcas.visualizar' => 'Marcas',
        'horarios.visualizar' => 'Horários'
    ];

    echo "=== VERIFICACAO FINAL DE PERMISSOES ===\n";
    $todasPermissoes = true;
    foreach ($permissoesCriticas as $codigo => $nome) {
        $tem = $permissionService->hasPermission($usuario, $codigo);
        echo ($tem ? "✅" : "❌") . " {$nome}: {$codigo}\n";
        if (!$tem) $todasPermissoes = false;
    }

    if ($todasPermissoes) {
        echo "\n🎉 SISTEMA 100% FUNCIONAL!\n";
        echo "🔗 TODOS OS LINKS LIBERADOS:\n";
        echo "📊 Dashboard: http://localhost:8000/comerciantes/dashboard\n";
        echo "🏢 Empresas: http://localhost:8000/comerciantes/empresas\n";
        echo "👥 Usuários: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
        echo "🏷️  Marcas: http://localhost:8000/comerciantes/marcas\n";
        echo "⏰ Horários: http://localhost:8000/comerciantes/empresas/1/horarios\n";
        echo "\n✅ PODE NAVEGAR SEM RESTRIÇÕES!\n";
    } else {
        echo "\n⚠️  Algumas permissões ainda precisam ser configuradas.\n";
    }
} else {
    echo "❌ Falha no login!\n";
}
