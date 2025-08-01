<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Config\ConfigDefinition;
use App\Models\Config\ConfigGroup;

try {
    echo "=== TESTE ESTRUTURA CONFIG CONTROLLER ===\n\n";

    // Simular o que o controller faz
    $empresaId = 1;

    // Buscar grupos ativos
    $grupos = ConfigGroup::where('empresa_id', $empresaId)
        ->where('ativo', true)
        ->orderBy('ordem')
        ->with(['definicoes' => function ($query) {
            $query->where('ativo', true)->orderBy('ordem');
        }])
        ->get();

    echo "Grupos encontrados: " . $grupos->count() . "\n";

    $configsByGroup = [];

    // Simular a criação da estrutura
    foreach ($grupos as $grupo) {
        echo "\nGrupo: {$grupo->nome}\n";
        echo "Definições: " . $grupo->definicoes->count() . "\n";

        $groupConfigs = [];
        foreach ($grupo->definicoes as $definicao) {
            // Simular valor atual
            $valorAtual = $definicao->valor_padrao ?? 'valor_exemplo';

            // Criar estrutura (igual ao controller corrigido)
            $config = (object) [
                'id' => $definicao->id,
                'chave' => $definicao->chave,
                'nome' => $definicao->nome ?? $definicao->chave,
                'tipo' => $definicao->tipo,
                'valor' => $valorAtual,
                'descricao' => $definicao->descricao,
                'valor_padrao' => $definicao->valor_padrao,
                'obrigatorio' => $definicao->obrigatorio,
                'avancado' => $definicao->avancado,
                'opcoes' => $definicao->opcoes,
                'editavel' => $definicao->editavel ?? true,
                'dica' => $definicao->dica,
                'grupo_nome' => $grupo->nome,
                'grupo_icone' => $grupo->icone_class ?? 'fas fa-cog'
            ];

            $groupConfigs[] = $config;

            echo "  - {$definicao->chave} (obrigatório: " . ($definicao->obrigatorio ? 'sim' : 'não') . ")\n";
        }

        if (!empty($groupConfigs)) {
            $configsByGroup[$grupo->nome] = $groupConfigs;
        }
    }

    echo "\n=== TESTE DA ESTRUTURA PARA VIEW ===\n";

    if (!empty($configsByGroup)) {
        echo "✓ configsByGroup não está vazio\n";

        foreach ($configsByGroup as $groupName => $groupConfigs) {
            echo "\nGrupo: {$groupName}\n";
            echo "Configs: " . count($groupConfigs) . "\n";

            if (!empty($groupConfigs)) {
                $firstConfig = $groupConfigs[0];
                echo "✓ Primeiro config tem grupo_icone: " . ($firstConfig->grupo_icone ?? 'N/A') . "\n";
                echo "✓ Primeiro config tem obrigatorio: " . ($firstConfig->obrigatorio ? 'sim' : 'não') . "\n";
                echo "✓ Primeiro config tem avancado: " . ($firstConfig->avancado ? 'sim' : 'não') . "\n";
                echo "✓ Primeiro config tem opcoes: " . (!empty($firstConfig->opcoes) ? 'sim' : 'não') . "\n";
            }
        }
    } else {
        echo "⚠️ configsByGroup está vazio\n";
    }

    echo "\n✅ TESTE CONCLUÍDO!\n";
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
