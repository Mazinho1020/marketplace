-- =================================================================
-- CONFIGURAÇÕES DO SISTEMA DE PAGAMENTOS
-- Data: 2025-08-02 07:07:15
-- Autor: Mazinho1020
-- =================================================================

-- Primeiro: Criar os grupos de configuração
INSERT INTO
    config_groups (
        empresa_id,
        codigo,
        nome,
        descricao,
        icone_class,
        ordem,
        ativo
    )
VALUES
    -- Grupos do Sistema de Planos
    (
        1,
        'planos_sistema',
        'Configurações de Planos',
        'Configurações gerais dos planos',
        'fas fa-layer-group',
        1,
        1
    ),
    (
        1,
        'planos_cobranca',
        'Configurações de Cobrança',
        'Configurações de cobrança e pagamento',
        'fas fa-money-bill',
        2,
        1
    ),
    (
        1,
        'planos_trial',
        'Configurações Trial',
        'Configurações do período trial',
        'fas fa-clock',
        3,
        1
    ),

-- Grupos do Sistema de Comerciantes
(
    1,
    'comerciantes_geral',
    'Configurações de Comerciantes',
    'Configurações gerais dos comerciantes',
    'fas fa-store',
    4,
    1
),
(
    1,
    'comerciantes_recursos',
    'Recursos e Limites',
    'Configurações de recursos e limites',
    'fas fa-cubes',
    5,
    1
),
(
    1,
    'comerciantes_notificacoes',
    'Notificações',
    'Configurações de notificações',
    'fas fa-bell',
    6,
    1
),

-- Grupos do Sistema de Afiliados
(
    1,
    'afiliados_geral',
    'Configurações de Afiliados',
    'Configurações gerais dos afiliados',
    'fas fa-handshake',
    7,
    1
),
(
    1,
    'afiliados_comissoes',
    'Comissões',
    'Configurações de comissões',
    'fas fa-percentage',
    8,
    1
),
(
    1,
    'afiliados_pagamentos',
    'Pagamentos',
    'Configurações de pagamentos',
    'fas fa-money-check',
    9,
    1
);

-- Depois: Inserir as configurações
INSERT INTO
    config_definitions (
        empresa_id,
        chave,
        nome,
        descricao,
        tipo,
        grupo_id,
        valor_padrao,
        obrigatorio,
        ordem
    )
VALUES
    -- Configurações de Planos
    (
        1,
        'planos_basic_mensal',
        'Preço Basic Mensal',
        'Preço do plano Basic mensal',
        'float',
        (
            SELECT id
            FROM config_groups
            WHERE
                codigo = 'planos_sistema'
        ),
        '97.00',
        1,
        1
    ),
    (
        1,
        'planos_premium_mensal',
        'Preço Premium Mensal',
        'Preço do plano Premium mensal',
        'float',
        (
            SELECT id
            FROM config_groups
            WHERE
                codigo = 'planos_sistema'
        ),
        '197.00',
        1,
        2
    ),
    (
        1,
        'planos_enterprise_mensal',
        'Preço Enterprise Mensal',
        'Preço do plano Enterprise mensal',
        'float',
        (
            SELECT id
            FROM config_groups
            WHERE
                codigo = 'planos_sistema'
        ),
        '397.00',
        1,
        3
    ),
    (
        1,
        'planos_desconto_anual',
        'Desconto Plano Anual',
        'Percentual de desconto para pagamento anual',
        'float',
        (
            SELECT id
            FROM config_groups
            WHERE
                codigo = 'planos_cobranca'
        ),
        '16.67',
        1,
        4
    ),
    (
        1,
        'planos_dias_trial',
        'Dias de Trial',
        'Quantidade de dias do período trial',
        'integer',
        (
            SELECT id
            FROM config_groups
            WHERE
                codigo = 'planos_trial'
        ),
        '7',
        1,
        5
    ),
    (
        1,
        'planos_grace_period',
        'Período de Carência',
        'Dias de carência após vencimento',
        'integer',
        (
            SELECT id
            FROM config_groups
            WHERE
                codigo = 'planos_cobranca'
        ),
        '3',
        1,
        6
    ),

-- Configurações de Comerciantes
(
    1,
    'comerciantes_dias_notificacao',
    'Dias para Notificar',
    'Dias antes do vencimento para notificar',
    'integer',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'comerciantes_notificacoes'
    ),
    '7',
    1,
    1
),
(
    1,
    'comerciantes_limite_usuarios_basic',
    'Limite Usuários Basic',
    'Limite de usuários do plano Basic',
    'integer',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'comerciantes_recursos'
    ),
    '3',
    1,
    2
),
(
    1,
    'comerciantes_limite_usuarios_premium',
    'Limite Usuários Premium',
    'Limite de usuários do plano Premium',
    'integer',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'comerciantes_recursos'
    ),
    '10',
    1,
    3
),
(
    1,
    'comerciantes_suspensao_automatica',
    'Suspensão Automática',
    'Suspender automaticamente após vencimento',
    'boolean',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'comerciantes_geral'
    ),
    '1',
    1,
    4
),
(
    1,
    'comerciantes_backup_retencao',
    'Retenção de Backup',
    'Dias para manter backup após cancelamento',
    'integer',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'comerciantes_geral'
    ),
    '90',
    1,
    5
),

-- Configurações de Afiliados
(
    1,
    'afiliados_comissao_padrao',
    'Comissão Padrão',
    'Percentual de comissão padrão',
    'float',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_comissoes'
    ),
    '20.00',
    1,
    1
),
(
    1,
    'afiliados_comissao_bronze',
    'Comissão Bronze',
    'Percentual de comissão nível Bronze',
    'float',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_comissoes'
    ),
    '25.00',
    1,
    2
),
(
    1,
    'afiliados_comissao_prata',
    'Comissão Prata',
    'Percentual de comissão nível Prata',
    'float',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_comissoes'
    ),
    '30.00',
    1,
    3
),
(
    1,
    'afiliados_comissao_ouro',
    'Comissão Ouro',
    'Percentual de comissão nível Ouro',
    'float',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_comissoes'
    ),
    '35.00',
    1,
    4
),
(
    1,
    'afiliados_saque_minimo',
    'Saque Mínimo',
    'Valor mínimo para solicitar saque',
    'float',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_pagamentos'
    ),
    '100.00',
    1,
    5
),
(
    1,
    'afiliados_dias_aprovacao',
    'Dias para Aprovação',
    'Dias para aprovar comissão após venda',
    'integer',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_geral'
    ),
    '15',
    1,
    6
),
(
    1,
    'afiliados_pix_ativo',
    'PIX Ativo',
    'Permitir pagamento via PIX',
    'boolean',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_pagamentos'
    ),
    '1',
    1,
    7
),
(
    1,
    'afiliados_ted_ativo',
    'TED Ativo',
    'Permitir pagamento via TED',
    'boolean',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_pagamentos'
    ),
    '1',
    1,
    8
),
(
    1,
    'afiliados_registro_automatico',
    'Registro Automático',
    'Aprovar afiliados automaticamente',
    'boolean',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_geral'
    ),
    '0',
    1,
    9
),
(
    1,
    'afiliados_cookie_dias',
    'Dias do Cookie',
    'Dias para manter cookie de afiliado',
    'integer',
    (
        SELECT id
        FROM config_groups
        WHERE
            codigo = 'afiliados_geral'
    ),
    '30',
    1,
    10
);

-- Inserir valores iniciais
INSERT INTO
    config_values (empresa_id, config_id, valor)
SELECT cd.empresa_id, cd.id, cd.valor_padrao
FROM config_definitions cd
WHERE
    cd.grupo_id IN (
        SELECT id
        FROM config_groups
        WHERE
            codigo IN (
                'planos_sistema',
                'planos_cobranca',
                'planos_trial',
                'comerciantes_geral',
                'comerciantes_recursos',
                'comerciantes_notificacoes',
                'afiliados_geral',
                'afiliados_comissoes',
                'afiliados_pagamentos'
            )
    );