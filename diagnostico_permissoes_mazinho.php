<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DIAGNOSTICO DE PERMISSOES - mazinho1@gmail.com ===\n";

$usuario = App\Comerciantes\Models\EmpresaUsuario::where('email', 'mazinho1@gmail.com')->first();

if (!$usuario) {
    echo "❌ Usuário não encontrado!\n";
    exit(1);
}

echo "👤 Usuário: {$usuario->nome} (ID: {$usuario->id})\n";
echo "📧 Email: {$usuario->email}\n";
echo "🏢 Empresa ID: {$usuario->empresa_id}\n\n";

// Fazer login para testar
Illuminate\Support\Facades\Auth::guard('comerciante')->login($usuario);

// Testar PermissionService
$permissionService = new App\Services\Permission\PermissionService();

echo "=== TESTANDO PERMISSOES ESPECIFICAS ===\n";

// Permissões que provavelmente são necessárias para os links
$permissoesTeste = [
    'empresas.visualizar',
    'empresas.listar',
    'empresas.index',
    'empresas.show',
    'usuarios.visualizar',
    'usuarios.listar',
    'usuarios.index',
    'marcas.visualizar',
    'marcas.listar',
    'marcas.index',
    'dashboard.visualizar'
];

foreach ($permissoesTeste as $permissao) {
    $temPermissao = $permissionService->hasPermission($usuario, $permissao);
    echo ($temPermissao ? "✅" : "❌") . " {$permissao}\n";
}

echo "\n=== VERIFICANDO MIDDLEWARE DE ROTAS ===\n";

// Simular o que o middleware faria para essas rotas
$rotasTeste = [
    ['rota' => 'comerciantes.empresas.index', 'acao' => 'index', 'metodo' => 'GET'],
    ['rota' => 'comerciantes.empresas.usuarios.index', 'acao' => 'usuarios', 'metodo' => 'GET'],
    ['rota' => 'comerciantes.marcas.index', 'acao' => 'index', 'metodo' => 'GET']
];

foreach ($rotasTeste as $teste) {
    // Simular a lógica do AutoPermissionCheck
    $recurso = 'empresas'; // extrair da rota
    $acao = 'visualizar'; // mapear GET + index = visualizar

    if (str_contains($teste['rota'], 'usuarios')) {
        $recurso = 'usuarios';
    } elseif (str_contains($teste['rota'], 'marcas')) {
        $recurso = 'marcas';
    }

    $permissaoEsperada = "{$recurso}.{$acao}";
    $temPermissao = $permissionService->hasPermission($usuario, $permissaoEsperada);

    echo ($temPermissao ? "✅" : "❌") . " Rota: {$teste['rota']} => Permissão: {$permissaoEsperada}\n";
}

echo "\n=== LISTANDO TODAS AS PERMISSOES DO USUARIO ===\n";

$permissoesUsuario = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
    ->join('empresa_permissoes', 'empresa_usuario_permissoes.permissao_id', '=', 'empresa_permissoes.id')
    ->where('empresa_usuario_permissoes.usuario_id', $usuario->id)
    ->where('empresa_usuario_permissoes.is_concedida', 1)
    ->select('empresa_permissoes.codigo', 'empresa_permissoes.nome')
    ->get();

echo "Total de permissões ativas: " . $permissoesUsuario->count() . "\n\n";

$empresasPerms = $permissoesUsuario->filter(function ($p) {
    return str_contains($p->codigo, 'empresas');
});
$usuariosPerms = $permissoesUsuario->filter(function ($p) {
    return str_contains($p->codigo, 'usuarios');
});
$marcasPerms = $permissoesUsuario->filter(function ($p) {
    return str_contains($p->codigo, 'marcas');
});

echo "EMPRESAS ({$empresasPerms->count()}):\n";
foreach ($empresasPerms as $p) {
    echo "  ✅ {$p->codigo} - {$p->nome}\n";
}

echo "\nUSUARIOS ({$usuariosPerms->count()}):\n";
foreach ($usuariosPerms as $p) {
    echo "  ✅ {$p->codigo} - {$p->nome}\n";
}

echo "\nMARCAS ({$marcasPerms->count()}):\n";
foreach ($marcasPerms as $p) {
    echo "  ✅ {$p->codigo} - {$p->nome}\n";
}
