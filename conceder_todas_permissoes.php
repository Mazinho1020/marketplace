<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CONCEDENDO TODAS AS PERMISSOES ===\n";

// Selecionar usuário (vou usar o ID 7 - mazinho1@gmail.com)
$usuarioId = 7;
$empresaId = 1; // Empresa padrão

$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
if (!$usuario) {
    echo "❌ Usuário ID {$usuarioId} não encontrado!\n";
    exit(1);
}

// Atualizar empresa_id se não tiver
if (empty($usuario->empresa_id)) {
    $usuario->empresa_id = $empresaId;
    $usuario->save();
    echo "✅ Empresa ID {$empresaId} atribuída ao usuário\n";
}

echo "👤 Concedendo permissões para: {$usuario->nome} ({$usuario->email})\n";
echo "🏢 Empresa ID: {$usuario->empresa_id}\n\n";

// Buscar todas as permissões existentes
$todasPermissoes = Illuminate\Support\Facades\DB::table('empresa_permissoes')->get();

echo "📋 Total de permissões no sistema: " . $todasPermissoes->count() . "\n";

// Limpar permissões existentes do usuário
Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
    ->where('usuario_id', $usuarioId)
    ->where('empresa_id', $usuario->empresa_id)
    ->delete();

echo "🗑️  Permissões antigas removidas\n";

// Conceder todas as permissões
$permissoesInseridas = 0;
foreach ($todasPermissoes as $permissao) {
    Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')->insert([
        'usuario_id' => $usuarioId,
        'empresa_id' => $usuario->empresa_id,
        'permissao_id' => $permissao->id,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    $permissoesInseridas++;
}

echo "✅ {$permissoesInseridas} permissões concedidas!\n\n";

// Verificar algumas permissões específicas importantes
$permissoesImportantes = [
    'usuarios.visualizar',
    'usuarios.criar',
    'usuarios.editar',
    'usuarios.excluir',
    'dashboard.visualizar',
    'empresas.visualizar'
];

echo "=== VERIFICACAO DAS PERMISSOES IMPORTANTES ===\n";
foreach ($permissoesImportantes as $permissaoNome) {
    $permissao = Illuminate\Support\Facades\DB::table('empresa_permissoes')
        ->where('nome', $permissaoNome)
        ->first();

    if ($permissao) {
        $temPermissao = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
            ->where('usuario_id', $usuarioId)
            ->where('empresa_id', $usuario->empresa_id)
            ->where('permissao_id', $permissao->id)
            ->exists();

        echo ($temPermissao ? "✅" : "❌") . " {$permissaoNome}\n";
    } else {
        echo "⚠️  {$permissaoNome} - NÃO EXISTE\n";
    }
}

echo "\n🎉 PROCESSO CONCLUÍDO! O usuário agora tem acesso total ao sistema.\n";
