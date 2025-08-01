<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== SIMULANDO EXATAMENTE O CONTROLLER ===\n\n";

    $groupCode = 'pdv';

    // Exatamente como no controller
    $group = DB::table('config_groups')
        ->where('codigo', $groupCode)
        ->where('ativo', true)
        ->first();

    if (!$group) {
        echo "❌ Grupo não encontrado!\n";
        exit;
    }

    echo "✅ Grupo: {$group->nome}\n\n";

    // Exatamente como no controller
    $configsRaw = DB::table('config_definitions as cd')
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

    echo "📋 ConfigsRaw count: " . count($configsRaw) . "\n\n";

    // Processar exatamente como no controller
    $configs = [];
    foreach ($configsRaw as $config) {
        $configs[$config->chave] = [
            'id' => $config->id,
            'nome' => $config->nome,
            'descricao' => $config->descricao,
            'tipo' => $config->tipo,
            'valor_atual' => $config->valor_atual,
            'valor_padrao' => $config->valor_padrao,
            'obrigatorio' => $config->obrigatorio,
            'dica' => $config->dica,
            'ajuda' => $config->ajuda,
            'regex_validacao' => $config->regex_validacao,
            'opcoes' => $config->opcoes,
            'site_id' => $config->site_id,
            'ambiente_id' => $config->ambiente_id
        ];
    }

    echo "📦 Configs processados count: " . count($configs) . "\n\n";

    foreach ($configs as $chave => $config) {
        echo "=== {$chave} ===\n";
        echo "Nome: {$config['nome']}\n";
        echo "Tipo: {$config['tipo']}\n";
        echo "Valor Atual: " . ($config['valor_atual'] ?? 'NULL') . "\n";
        echo "Valor Padrão: " . ($config['valor_padrao'] ?? 'NULL') . "\n";
        echo "Obrigatório: " . ($config['obrigatorio'] ? 'Sim' : 'Não') . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
