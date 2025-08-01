<?php
// Configuração Simplificada de Banco de Dados
// Última atualização: 2025-08-01 02:45:00

return [
    // Ambiente atual: desenvolvimento, homologacao ou producao
    // Altere este valor para trocar de ambiente
    'ambiente' => 'desenvolvimento',

    // Configurações das conexões por ambiente
    'conexoes' => [
        'desenvolvimento' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'porta' => 3306,
            'banco' => 'meufinanceiro',
            'usuario' => 'root',
            'senha' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefixo' => '',
            'habilitado' => true,
        ],
        'homologacao' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'porta' => 3306,
            'banco' => 'meufinanceiro_homolog',
            'usuario' => 'root',
            'senha' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefixo' => '',
            'habilitado' => false,
        ],
        'producao' => [
            'driver' => 'mysql',
            'host' => '162.241.2.71',
            'porta' => 3306,
            'banco' => 'finanp06_meufinanceiro',
            'usuario' => 'finanp06_tradicao',
            'senha' => 'Mazinho2512@',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefixo' => '',
            'habilitado' => true,
        ],
    ],

    // Detecção automática de ambiente baseada no hostname/diretório
    'deteccao_auto' => true,

    // Log de mudanças
    'historico' => [],
];
