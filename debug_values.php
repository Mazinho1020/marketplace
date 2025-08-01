<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== DEBUG: Investigando problema dos valores vazios ===\n\n";

    $groupCode = 'pdv';

    // 1. Verificar se o grupo existe
    $group = DB::table('config_groups')
        ->where('codigo', $groupCode)
        ->where('ativo', true)
        ->first();

    if (!$group) {
        echo "❌ Grupo '$groupCode' não encontrado!\n";
        exit;
    }

    echo "✅ Grupo encontrado: {$group->nome} (ID: {$group->id})\n\n";

    // 2. Verificar configurações do grupo
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

    echo "📋 Configurações encontradas: " . count($configs) . "\n\n";

    foreach ($configs as $config) {
        echo "--- {$config->chave} ---\n";
        echo "Nome: {$config->nome}\n";
        echo "Tipo: {$config->tipo}\n";
        echo "Valor Atual: " . ($config->valor_atual ?? 'NULL') . "\n";
        echo "Valor Padrão: " . ($config->valor_padrao ?? 'NULL') . "\n";
        echo "Site ID: " . ($config->site_id ?? 'NULL') . "\n";
        echo "Ambiente ID: " . ($config->ambiente_id ?? 'NULL') . "\n";
        echo "Obrigatório: " . ($config->obrigatorio ? 'Sim' : 'Não') . "\n\n";
    }

    // 3. Verificar como os dados chegam na view (simular o controller)
    echo "=== SIMULANDO CONTROLLER GROUP ===\n";
    $configsArray = $configs->keyBy('chave')->toArray();

    echo "Configurações como array:\n";
    foreach ($configsArray as $chave => $config) {
        echo "[$chave] => Valor atual: " . ($config->valor_atual ?? 'VAZIO') . " | Valor padrão: " . ($config->valor_padrao ?? 'VAZIO') . "\n";
    }

    // 4. Verificar sites e ambientes
    echo "\n=== SITES DISPONÍVEIS ===\n";
    $sites = DB::table('config_sites')->select('id', 'codigo', 'nome')->where('ativo', true)->get();
    foreach ($sites as $site) {
        echo "- {$site->id}: {$site->codigo} ({$site->nome})\n";
    }

    echo "\n=== AMBIENTES DISPONÍVEIS ===\n";
    $environments = DB::table('config_environments')->select('id', 'codigo', 'nome')->where('ativo', true)->get();
    foreach ($environments as $env) {
        echo "- {$env->id}: {$env->codigo} ({$env->nome})\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
