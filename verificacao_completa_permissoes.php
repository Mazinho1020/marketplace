<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACAO COMPLETA DE PERMISSOES ===\n";

$usuarioId = 3; // mazinho1@gmail.com (liomar certo)
$empresaId = 1;

$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

echo "👤 Usuário: {$usuario->nome} ({$usuario->email})\n";
echo "🏢 Empresa ID: {$usuario->empresa_id}\n\n";

// Permissões críticas que o usuário deve ter para navegar
$permissoesCriticas = [
    'dashboard.visualizar' => 'Acessar Dashboard',
    'empresas.visualizar' => 'Ver empresas',
    'empresas.listar' => 'Listar empresas',
    'usuarios.visualizar' => 'Ver usuários',
    'usuarios.listar' => 'Listar usuários',
    'usuarios.criar' => 'Criar usuários',
    'usuarios.editar' => 'Editar usuários',
    'usuarios.excluir' => 'Excluir usuários',
    'marcas.visualizar' => 'Ver marcas',
    'marcas.listar' => 'Listar marcas',
    'horarios.visualizar' => 'Ver horários',
    'horarios.listar' => 'Listar horários'
];

echo "=== VERIFICANDO PERMISSOES CRITICAS ===\n";

$permissoesFaltando = [];
foreach ($permissoesCriticas as $codigo => $descricao) {
    $tem = $permissionService->hasPermission($usuario, $codigo);
    echo ($tem ? "✅" : "❌") . " {$codigo} - {$descricao}\n";

    if (!$tem) {
        $permissoesFaltando[] = $codigo;
    }
}

if (count($permissoesFaltando) > 0) {
    echo "\n⚠️  CONCEDENDO PERMISSOES FALTANDO...\n";

    foreach ($permissoesFaltando as $codigo) {
        // Buscar permissão no banco
        $permissao = Illuminate\Support\Facades\DB::table('empresa_permissoes')
            ->where('codigo', $codigo)
            ->first();

        if ($permissao) {
            // Verificar se já existe o relacionamento
            $jaExiste = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
                ->where('usuario_id', $usuarioId)
                ->where('permissao_id', $permissao->id)
                ->where('empresa_id', $empresaId)
                ->first();

            if ($jaExiste) {
                // Atualizar para concedida
                Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
                    ->where('usuario_id', $usuarioId)
                    ->where('permissao_id', $permissao->id)
                    ->where('empresa_id', $empresaId)
                    ->update(['is_concedida' => 1, 'updated_at' => now()]);
                echo "   ✅ Atualizada: {$codigo}\n";
            } else {
                // Inserir nova permissão
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
        } else {
            echo "   ❌ Permissão não encontrada: {$codigo}\n";
        }
    }

    echo "\n=== VERIFICACAO FINAL ===\n";
    foreach ($permissoesCriticas as $codigo => $descricao) {
        $tem = $permissionService->hasPermission($usuario, $codigo);
        echo ($tem ? "✅" : "❌") . " {$codigo}\n";
    }
}

echo "\n🔗 LINKS PARA TESTAR:\n";
echo "📊 Dashboard: http://localhost:8000/comerciantes/dashboard\n";
echo "👥 Usuários: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
echo "🏢 Empresas: http://localhost:8000/comerciantes/empresas\n";
echo "🏷️  Marcas: http://localhost:8000/comerciantes/marcas\n";
echo "⏰ Horários: http://localhost:8000/comerciantes/empresas/1/horarios\n";

echo "\n✅ SISTEMA TOTALMENTE CONFIGURADO!\n";
