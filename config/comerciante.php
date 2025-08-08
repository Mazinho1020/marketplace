<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações do Módulo Comerciante
    |--------------------------------------------------------------------------
    */

    // Configurações gerais
    'empresa_padrao' => env('COMERCIANTE_EMPRESA_PADRAO', 2),
    'cache_ttl' => env('COMERCIANTE_CACHE_TTL', 3600),

    // Pessoas
    'pessoas' => [
        'cpf_obrigatorio' => env('PESSOAS_CPF_OBRIGATORIO', true),
        'email_obrigatorio' => env('PESSOAS_EMAIL_OBRIGATORIO', false),
        'telefone_obrigatorio' => env('PESSOAS_TELEFONE_OBRIGATORIO', true),
        'endereco_obrigatorio' => env('PESSOAS_ENDERECO_OBRIGATORIO', false),
        'limite_credito_padrao' => env('PESSOAS_LIMITE_CREDITO_PADRAO', 500.00),
        'limite_fiado_padrao' => env('PESSOAS_LIMITE_FIADO_PADRAO', 100.00),
        'prazo_pagamento_cliente' => env('PESSOAS_PRAZO_PAGAMENTO_CLIENTE', 30),
    ],

    // RH
    'rh' => [
        'dia_fechamento_folha' => env('RH_DIA_FECHAMENTO_FOLHA', 25),
        'dia_pagamento_folha' => env('RH_DIA_PAGAMENTO_FOLHA', 5),
        'salario_minimo_referencia' => env('RH_SALARIO_MINIMO_REFERENCIA', 1412.00),
        'gerar_conta_pagar_folha' => env('RH_GERAR_CONTA_PAGAR_FOLHA', true),
        'vale_transporte_percentual' => env('RH_VALE_TRANSPORTE_PERCENTUAL', 6.00),
        'vale_alimentacao_valor' => env('RH_VALE_ALIMENTACAO_VALOR', 25.00),
    ],

    // Vendas
    'vendas' => [
        'cliente_obrigatorio_pdv' => env('VENDAS_CLIENTE_OBRIGATORIO_PDV', false),
        'vendedor_obrigatorio' => env('VENDAS_VENDEDOR_OBRIGATORIO', true),
        'gerar_conta_receber' => env('VENDAS_GERAR_CONTA_RECEBER', true),
        'verificar_limite_credito' => env('VENDAS_VERIFICAR_LIMITE_CREDITO', true),
        'bloquear_cliente_inadimplente' => env('VENDAS_BLOQUEAR_CLIENTE_INADIMPLENTE', true),
    ],

    // Financeiro
    'financeiro' => [
        'prazo_pagamento_padrao' => env('FINANCEIRO_PRAZO_PAGAMENTO_PADRAO', 30),
        'juros_atraso_percentual' => env('FINANCEIRO_JUROS_ATRASO_PERCENTUAL', 2.00),
        'multa_atraso_percentual' => env('FINANCEIRO_MULTA_ATRASO_PERCENTUAL', 10.00),
        'desconto_antecipacao_percentual' => env('FINANCEIRO_DESCONTO_ANTECIPACAO_PERCENTUAL', 5.00),
    ],

    // Integração
    'integracao' => [
        'auto_sync_enabled' => env('INTEGRACAO_AUTO_SYNC_ENABLED', true),
        'sync_interval_minutes' => env('INTEGRACAO_SYNC_INTERVAL_MINUTES', 30),
        'webhook_enabled' => env('INTEGRACAO_WEBHOOK_ENABLED', false),
        'api_rate_limit' => env('INTEGRACAO_API_RATE_LIMIT', 1000),
    ],

    // Validações
    'validacoes' => [
        'cpf_regex' => '/^\d{3}\.\d{3}\.\d{3}-\d{2}$|^\d{11}$/',
        'cnpj_regex' => '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$|^\d{14}$/',
        'cep_regex' => '/^\d{5}-?\d{3}$/',
        'telefone_regex' => '/^\(\d{2}\)\s?\d{4,5}-?\d{4}$|^\d{10,11}$/',
        'email_regex' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
    ],

    // Upload de arquivos
    'uploads' => [
        'max_size' => env('COMERCIANTE_UPLOAD_MAX_SIZE', 10485760), // 10MB
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'],
        'path' => env('COMERCIANTE_UPLOAD_PATH', 'comerciante'),
        'documentos_path' => env('COMERCIANTE_DOCUMENTOS_PATH', 'comerciante/documentos'),
        'fotos_path' => env('COMERCIANTE_FOTOS_PATH', 'comerciante/fotos'),
    ],

    // Notificações
    'notificacoes' => [
        'enabled' => env('COMERCIANTE_NOTIFICACOES_ENABLED', true),
        'email_enabled' => env('COMERCIANTE_EMAIL_ENABLED', true),
        'sms_enabled' => env('COMERCIANTE_SMS_ENABLED', false),
        'whatsapp_enabled' => env('COMERCIANTE_WHATSAPP_ENABLED', false),
        'push_enabled' => env('COMERCIANTE_PUSH_ENABLED', true),
    ],

    // Relatórios
    'relatorios' => [
        'cache_enabled' => env('COMERCIANTE_RELATORIOS_CACHE_ENABLED', true),
        'cache_ttl' => env('COMERCIANTE_RELATORIOS_CACHE_TTL', 1800), // 30 min
        'export_limit' => env('COMERCIANTE_EXPORT_LIMIT', 10000),
        'pdf_orientation' => env('COMERCIANTE_PDF_ORIENTATION', 'portrait'),
    ],

    // Segurança
    'seguranca' => [
        'log_changes' => env('COMERCIANTE_LOG_CHANGES', true),
        'audit_enabled' => env('COMERCIANTE_AUDIT_ENABLED', true),
        'backup_enabled' => env('COMERCIANTE_BACKUP_ENABLED', true),
        'encrypt_sensitive_data' => env('COMERCIANTE_ENCRYPT_SENSITIVE_DATA', true),
    ],
];
