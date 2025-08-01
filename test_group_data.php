<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Teste de dados para view group...\n\n";

    $groupCode = 'sistema';

    // Buscar o grupo
    $group = DB::table('config_groups')
        ->where('codigo', $groupCode)
        ->where('ativo', true)
        ->first();

    if (!$group) {
        echo "❌ Grupo '$groupCode' não encontrado!\n";
        exit;
    }

    echo "✅ Grupo encontrado:\n";
    echo "- ID: {$group->id}\n";
    echo "- Nome: {$group->nome}\n";
    echo "- Descrição: " . ($group->descricao ?? 'N/A') . "\n\n";

    // Buscar configurações do grupo
    $configs = DB::table('config_definitions as cd')
        ->leftJoin('config_values as cv', 'cd.id', '=', 'cv.config_id')
        ->where('cd.grupo_id', $group->id)
        ->where('cd.ativo', true)
        ->select([
            'cd.*',
            'cv.valor as valor_atual',
            'cv.site_id',
            'cv.ambiente_id'
        ])
        ->orderBy('cd.ordem')
        ->get();

    echo "✅ Configurações encontradas: " . count($configs) . "\n\n";

    foreach ($configs as $config) {
        echo "=== {$config->chave} ===\n";
        echo "Nome: {$config->nome}\n";
        echo "Tipo: {$config->tipo}\n";
        echo "Valor Atual: " . ($config->valor_atual ?? 'NULL') . "\n";
        echo "Valor Padrão: " . ($config->valor_padrao ?? 'NULL') . "\n";
        echo "Obrigatório: " . ($config->obrigatorio ? 'Sim' : 'Não') . "\n";
        echo "Descrição: " . ($config->descricao ?? 'N/A') . "\n";
        echo "Dica: " . ($config->dica ?? 'N/A') . "\n";
        echo "Ajuda: " . ($config->ajuda ?? 'N/A') . "\n";
        echo "\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
