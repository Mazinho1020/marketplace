<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ANALISANDO PERMISSOES DO USUARIO 7 ===\n";

// Verificar permissões diretas do usuário
$permissoesUsuario = Illuminate\Support\Facades\DB::table('empresa_usuario_permissoes')
    ->where('usuario_id', 7)
    ->get();

echo "Total de permissões diretas: " . $permissoesUsuario->count() . "\n\n";

if ($permissoesUsuario->count() > 0) {
    echo "DETALHES DAS PERMISSÕES:\n";
    foreach ($permissoesUsuario->take(5) as $p) {
        $permissaoInfo = Illuminate\Support\Facades\DB::table('empresa_permissoes')
            ->where('id', $p->permissao_id)
            ->first();

        echo "- Permissão ID: {$p->permissao_id}\n";
        echo "  Nome: " . ($permissaoInfo ? $permissaoInfo->nome : 'N/A') . "\n";
        echo "  Código: " . ($permissaoInfo ? ($permissaoInfo->codigo ?? 'SEM CÓDIGO') : 'N/A') . "\n";
        echo "  Is Concedida: " . (isset($p->is_concedida) ? ($p->is_concedida ? 'SIM' : 'NÃO') : 'CAMPO NÃO EXISTE') . "\n";
        echo "---\n";
    }
}

// Verificar se o campo is_concedida existe na tabela
echo "\n=== ESTRUTURA DA TABELA ===\n";
try {
    $colunas = Illuminate\Support\Facades\Schema::getColumnListing('empresa_usuario_permissoes');
    echo "Colunas da tabela empresa_usuario_permissoes:\n";
    foreach ($colunas as $coluna) {
        echo "- {$coluna}\n";
    }
} catch (Exception $e) {
    echo "Erro ao obter estrutura: " . $e->getMessage() . "\n";
}
