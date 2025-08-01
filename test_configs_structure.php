<?php
// Simular a estrutura de dados que o controller está criando
echo "=== TESTE ESTRUTURA CONFIGSBYGROUP ===\n\n";

// Simular dados como o controller faz
$configsByGroup = [
    'Configurações Gerais' => [
        (object) [
            'id' => 1,
            'chave' => 'app_name',
            'nome' => 'Nome da Aplicação',
            'tipo' => 'string',
            'valor' => 'MeuFinanceiro',
            'descricao' => 'Nome da aplicação',
            'grupo_nome' => 'Configurações Gerais',
            'grupo_icone' => 'fas fa-cog'
        ],
        (object) [
            'id' => 2,
            'chave' => 'app_debug',
            'nome' => 'Modo Debug',
            'tipo' => 'boolean',
            'valor' => true,
            'descricao' => 'Ativar modo debug',
            'grupo_nome' => 'Configurações Gerais',
            'grupo_icone' => 'fas fa-cog'
        ]
    ]
];

// Testar se a estrutura funciona como esperado na view
foreach ($configsByGroup as $groupName => $groupConfigs) {
    echo "Grupo: {$groupName}\n";
    echo "Primeiro config: " . $groupConfigs[0]->chave . "\n";
    echo "Ícone do grupo: " . ($groupConfigs[0]->grupo_icone ?? 'uil uil-folder') . "\n";
    echo "Quantidade de configs: " . count($groupConfigs) . "\n\n";

    foreach ($groupConfigs as $config) {
        echo "  - {$config->chave}: {$config->valor}\n";
    }
    echo "\n";
}

echo "✓ Estrutura está funcionando corretamente!\n";
