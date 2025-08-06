<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== AUDIT COMPLETO DE PERMISSOES CRUD ===\n";

$usuarioId = 3; // mazinho1@gmail.com
$empresaId = 1;

// Recursos principais do sistema
$recursos = ['empresas', 'usuarios', 'marcas', 'horarios'];
$acoes = ['visualizar', 'listar', 'criar', 'editar', 'excluir'];

$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

echo "👤 Auditando usuário: {$usuario->nome} ({$usuario->email})\n\n";

$permissoesFaltando = [];

foreach ($recursos as $recurso) {
    echo "=== {$recurso} ===\n";

    foreach ($acoes as $acao) {
        $codigoPermissao = "{$recurso}.{$acao}";

        // Verificar se a permissão existe no banco
        $permissaoExiste = Illuminate\Support\Facades\DB::table('empresa_permissoes')
            ->where('codigo', $codigoPermissao)
            ->first();

        if (!$permissaoExiste) {
            echo "❌ {$codigoPermissao} - NÃO EXISTE\n";
            continue;
        }

        // Testar com PermissionService
        $tem = $permissionService->hasPermission($usuario, $codigoPermissao);
        echo ($tem ? "✅" : "❌") . " {$codigoPermissao}\n";

        if (!$tem) {
            $permissoesFaltando[] = $codigoPermissao;
        }
    }
    echo "\n";
}

// Conceder permissões que estão faltando
if (count($permissoesFaltando) > 0) {
    echo "⚠️  CONCEDENDO PERMISSOES FALTANDO...\n";

    foreach ($permissoesFaltando as $codigo) {
        $permissao = Illuminate\Support\Facades\DB::table('empresa_permissoes')
            ->where('codigo', $codigo)
            ->first();

        if ($permissao) {
            $jaExiste = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
                ->where('usuario_id', $usuarioId)
                ->where('permissao_id', $permissao->id)
                ->where('empresa_id', $empresaId)
                ->first();

            if ($jaExiste) {
                Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
                    ->where('usuario_id', $usuarioId)
                    ->where('permissao_id', $permissao->id)
                    ->where('empresa_id', $empresaId)
                    ->update(['is_concedida' => 1, 'updated_at' => now()]);
                echo "   ✅ Atualizada: {$codigo}\n";
            } else {
                Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insert([
                    'usuario_id' => $usuarioId,
                    'empresa_id' => $empresaId,
                    'permissao_id' => $permissao->id,
                    'is_concedida' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "   ➕ Concedida: {$codigo}\n";
            }
        }
    }

    // Limpar cache
    Illuminate\Support\Facades\Cache::flush();

    echo "\n=== VERIFICACAO FINAL ===\n";
    foreach ($recursos as $recurso) {
        echo "📁 {$recurso}:\n";
        foreach ($acoes as $acao) {
            $codigoPermissao = "{$recurso}.{$acao}";
            $tem = $permissionService->hasPermission($usuario, $codigoPermissao);
            echo "   " . ($tem ? "✅" : "❌") . " {$acao}\n";
        }
        echo "\n";
    }
}

// Incluir dashboard
echo "=== DASHBOARD ===\n";
$temDashboard = $permissionService->hasPermission($usuario, 'dashboard.visualizar');
echo ($temDashboard ? "✅" : "❌") . " dashboard.visualizar\n";

if (!$temDashboard) {
    $dashboardPerm = Illuminate\Support\Facades\DB::table('empresa_permissoes')
        ->where('codigo', 'dashboard.visualizar')
        ->first();

    if ($dashboardPerm) {
        Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insertOrIgnore([
            'usuario_id' => $usuarioId,
            'empresa_id' => $empresaId,
            'permissao_id' => $dashboardPerm->id,
            'is_concedida' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ➕ Dashboard concedido!\n";
    }
}

echo "\n🎉 AUDIT COMPLETO!\n";
echo "📊 O usuário agora tem acesso CRUD completo a todo o sistema!\n";
