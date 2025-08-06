<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACAO E CORREÇÃO: HORARIOS ===\n";

$usuarioId = 3; // mazinho1@gmail.com
$empresaId = 1;

// Verificar se existem permissões de horários
echo "=== VERIFICANDO PERMISSOES DE HORARIOS ===\n";
$permissoesHorarios = Illuminate\Support\Facades\DB::table('empresa_permissoes')
    ->where('codigo', 'LIKE', '%horario%')
    ->get();

if ($permissoesHorarios->count() > 0) {
    echo "✅ Encontradas " . $permissoesHorarios->count() . " permissões de horários:\n";
    foreach ($permissoesHorarios as $p) {
        echo "   • {$p->codigo} - {$p->nome}\n";
    }
} else {
    echo "❌ Nenhuma permissão de horários encontrada. Criando...\n";

    $permissoesHorariosPadrao = [
        ['codigo' => 'horarios.visualizar', 'nome' => 'Ver Horários', 'descricao' => 'Visualizar horários de funcionamento'],
        ['codigo' => 'horarios.listar', 'nome' => 'Listar Horários', 'descricao' => 'Ver lista de horários'],
        ['codigo' => 'horarios.criar', 'nome' => 'Criar Horários', 'descricao' => 'Criar novos horários'],
        ['codigo' => 'horarios.editar', 'nome' => 'Editar Horários', 'descricao' => 'Editar horários existentes'],
        ['codigo' => 'horarios.excluir', 'nome' => 'Excluir Horários', 'descricao' => 'Excluir horários'],
        ['codigo' => 'horarios.padrao.visualizar', 'nome' => 'Ver Horários Padrão', 'descricao' => 'Visualizar horários padrão'],
        ['codigo' => 'horarios.excecoes.visualizar', 'nome' => 'Ver Exceções de Horário', 'descricao' => 'Visualizar exceções de horário'],
    ];

    foreach ($permissoesHorariosPadrao as $perm) {
        $id = Illuminate\Support\Facades\DB::table('empresa_permissoes')->insertGetId([
            'codigo' => $perm['codigo'],
            'nome' => $perm['nome'],
            'descricao' => $perm['descricao'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "   ✅ Criada: {$perm['codigo']} (ID: {$id})\n";
    }

    // Atualizar a lista
    $permissoesHorarios = Illuminate\Support\Facades\DB::table('empresa_permissoes')
        ->where('codigo', 'LIKE', '%horario%')
        ->get();
}

// Conceder todas as permissões de horários ao usuário
echo "\n=== CONCEDENDO PERMISSOES DE HORARIOS AO USUARIO ===\n";
foreach ($permissoesHorarios as $perm) {
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
    } else {
        echo "   ℹ️  {$perm->codigo} (já existe)\n";
    }
}

// Testar as permissões
echo "\n=== TESTANDO PERMISSOES ===\n";
Illuminate\Support\Facades\Cache::flush();

$usuario = App\Comerciantes\Models\EmpresaUsuario::find($usuarioId);
$permissionService = new App\Services\Permission\PermissionService();

$permissoesTeste = [
    'horarios.visualizar',
    'horarios.listar',
    'marcas.visualizar',
    'empresas.visualizar',
    'usuarios.visualizar'
];

foreach ($permissoesTeste as $perm) {
    $tem = $permissionService->hasPermission($usuario, $perm);
    echo ($tem ? "✅" : "❌") . " {$perm}: " . ($tem ? 'TRUE' : 'FALSE') . "\n";
}

echo "\n🎉 TODAS AS PERMISSOES CONFIGURADAS!\n";
echo "🔗 LINKS PARA TESTAR:\n";
echo "🏷️  Marcas: http://localhost:8000/comerciantes/marcas\n";
echo "⏰ Horários: http://localhost:8000/comerciantes/empresas/1/horarios\n";
echo "👥 Usuários: http://localhost:8000/comerciantes/empresas/1/usuarios\n";
