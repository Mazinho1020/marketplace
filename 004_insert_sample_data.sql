-- =================================================================
-- DADOS DE EXEMPLO REALÍSTICOS
-- =================================================================

-- ===== GATEWAYS =====
INSERT INTO
    payment_gateways (
        provider,
        name,
        environment,
        is_active,
        priority,
        credentials,
        supported_methods,
        webhook_url,
        success_url,
        cancel_url,
        fees_config
    )
VALUES (
        'safe2pay',
        'Safe2Pay Production',
        'production',
        TRUE,
        1,
        '{"token": "E8FA28B86AAD45589B80294D01639AE0", "secret": "84165F50AFDB402FBD5EF8A83109ADC79E6C8B8FD15C40E483E31317B6F7E5BB"}',
        '["pix", "credit_card", "debit_card", "bank_slip"]',
        'https://meufinanceiro.com/api/webhooks/safe2pay',
        'https://meufinanceiro.com/payment/success',
        'https://meufinanceiro.com/payment/cancel',
        '{"pix": 0.99, "credit_card": 3.49, "bank_slip": 2.50}'
    ),
    (
        'safe2pay',
        'Safe2Pay Sandbox',
        'sandbox',
        TRUE,
        2,
        '{"token": "SANDBOX_TOKEN_123", "secret": "SANDBOX_SECRET_456"}',
        '["pix", "credit_card", "bank_slip"]',
        'https://dev.meufinanceiro.com/api/webhooks/safe2pay',
        'https://dev.meufinanceiro.com/payment/success',
        'https://dev.meufinanceiro.com/payment/cancel',
        '{"pix": 0.99, "credit_card": 3.49, "bank_slip": 2.50}'
    );

-- ===== PLANOS =====
INSERT INTO
    subscription_plans (
        code,
        name,
        description,
        price_monthly,
        price_yearly,
        price_lifetime,
        features,
        limits,
        trial_days
    )
VALUES (
        'basic',
        'Plano Básico',
        'Sistema financeiro básico para pequenas empresas',
        97.00,
        970.00,
        1997.00,
        '["financeiro", "relatorios_basicos", "backup_automatico"]',
        '{"users": 3, "companies": 1, "transactions_per_month": 1000}',
        7
    ),
    (
        'premium',
        'Plano Premium',
        'Sistema completo com PDV e Delivery',
        197.00,
        1970.00,
        2997.00,
        '["financeiro", "pdv", "delivery", "relatorios_avancados", "backup_automatico", "suporte_prioritario"]',
        '{"users": 10, "companies": 1, "transactions_per_month": 10000}',
        7
    ),
    (
        'enterprise',
        'Plano Enterprise',
        'Solução completa para grandes empresas',
        397.00,
        3970.00,
        5997.00,
        '["financeiro", "pdv", "delivery", "multi_empresa", "relatorios_avancados", "api", "backup_automatico", "suporte_24h"]',
        '{"users": 50, "companies": 5, "transactions_per_month": 50000}',
        14
    ),
    (
        'black_friday',
        'Oferta Black Friday',
        'Premium com 50% de desconto',
        98.50,
        985.00,
        1498.50,
        '["financeiro", "pdv", "delivery", "relatorios_avancados", "backup_automatico"]',
        '{"users": 10, "companies": 1, "transactions_per_month": 10000}',
        7
    );

-- ===== COMERCIANTES =====
INSERT INTO
    merchants (
        company_name,
        trading_name,
        document,
        email,
        phone,
        address_street,
        address_city,
        address_state,
        status,
        license_key
    )
VALUES (
        'Pizzaria do João Ltda',
        'Pizzaria do João',
        '12.345.678/0001-90',
        'joao@pizzariadojoao.com',
        '(11) 99999-1234',
        'Rua das Pizzas, 123',
        'São Paulo',
        'SP',
        'active',
        'PRD-PREMIUM-202508-A7F9X2M4'
    ),
    (
        'Loja da Maria ME',
        'Loja da Maria',
        '98.765.432/0001-10',
        'maria@lojadamaria.com',
        '(11) 88888-5678',
        'Av. Comercial, 456',
        'São Paulo',
        'SP',
        'trial',
        'TRL-BASIC-202508-B8G0Y3N5'
    ),
    (
        'Empresa ABC Ltda',
        'ABC Corp',
        '11.222.333/0001-44',
        'admin@abccorp.com',
        '(11) 77777-9012',
        'Rua Empresarial, 789',
        'São Paulo',
        'SP',
        'active',
        'PRD-ENTERPRISE-202507-C9H1Z4O6'
    ),
    (
        'Burguer House Ltda',
        'Burguer House',
        '55.666.777/0001-88',
        'contato@burguerhouse.com',
        '(11) 66666-3456',
        'Rua dos Hambúrgueres, 321',
        'São Paulo',
        'SP',
        'suspended',
        'SUS-PREMIUM-202507-D0I2A5P7'
    ),
    (
        'Mercado Central ME',
        'Mercado Central',
        '22.333.444/0001-22',
        'gerencia@mercadocentral.com',
        '(11) 55555-7890',
        'Av. Central, 654',
        'São Paulo',
        'SP',
        'active',
        'PRD-BASIC-202508-E1J3B6Q8'
    );

-- ===== ASSINATURAS DOS COMERCIANTES =====
INSERT INTO
    merchant_subscriptions (
        merchant_id,
        plan_id,
        plan_code,
        plan_name,
        features,
        limits,
        billing_cycle,
        amount,
        status,
        started_at,
        expires_at,
        next_payment_at,
        auto_renewal
    )
VALUES (
        1,
        2,
        'premium',
        'Plano Premium',
        '["financeiro", "pdv", "delivery", "relatorios_avancados", "backup_automatico", "suporte_prioritario"]',
        '{"users": 10, "companies": 1, "transactions_per_month": 10000}',
        'monthly',
        197.00,
        'active',
        '2025-07-15 09:00:00',
        '2025-08-15 09:00:00',
        '2025-08-15 06:00:00',
        TRUE
    ),
    (
        2,
        1,
        'basic',
        'Plano Básico',
        '["financeiro", "relatorios_basicos", "backup_automatico"]',
        '{"users": 3, "companies": 1, "transactions_per_month": 1000}',
        'monthly',
        97.00,
        'trial',
        '2025-08-01 10:00:00',
        NULL,
        NULL,
        TRUE
    ),
    (
        3,
        3,
        'enterprise',
        'Plano Enterprise',
        '["financeiro", "pdv", "delivery", "multi_empresa", "relatorios_avancados", "api", "backup_automatico", "suporte_24h"]',
        '{"users": 50, "companies": 5, "transactions_per_month": 50000}',
        'yearly',
        3970.00,
        'active',
        '2024-08-30 14:00:00',
        '2025-08-30 14:00:00',
        '2025-08-30 06:00:00',
        TRUE
    ),
    (
        4,
        2,
        'premium',
        'Plano Premium',
        '["financeiro", "pdv", "delivery", "relatorios_avancados", "backup_automatico", "suporte_prioritario"]',
        '{"users": 10, "companies": 1, "transactions_per_month": 10000}',
        'monthly',
        197.00,
        'suspended',
        '2025-06-05 08:00:00',
        '2025-08-05 08:00:00',
        NULL,
        FALSE
    ),
    (
        5,
        1,
        'basic',
        'Plano Básico',
        '["financeiro", "relatorios_basicos", "backup_automatico"]',
        '{"users": 3, "companies": 1, "transactions_per_month": 1000}',
        'monthly',
        97.00,
        'active',
        '2025-07-10 11:00:00',
        '2025-08-10 11:00:00',
        '2025-08-10 06:00:00',
        TRUE
    );

-- ===== AFILIADOS =====
INSERT INTO
    affiliates (
        name,
        email,
        phone,
        document,
        affiliate_code,
        tier,
        commission_rate,
        pix_key,
        pix_key_type,
        total_sales,
        total_commissions,
        status,
        approved_at
    )
VALUES (
        'João Silva',
        'joao.silva@email.com',
        '(11) 99999-0001',
        '123.456.789-01',
        'JS2025001',
        'gold',
        35.00,
        'joao.silva@email.com',
        'email',
        15780.00,
        3945.00,
        'active',
        '2025-01-15 10:00:00'
    ),
    (
        'Maria Santos',
        'maria.santos@email.com',
        '(11) 99999-0002',
        '987.654.321-02',
        'MS2025002',
        'silver',
        30.00,
        '987.654.321-02',
        'cpf',
        12340.00,
        2776.50,
        'active',
        '2025-02-10 14:30:00'
    ),
    (
        'Carlos Pereira',
        'carlos.pereira@email.com',
        '(11) 99999-0003',
        '456.789.123-03',
        'CP2025003',
        'silver',
        30.00,
        '+5511999990003',
        'phone',
        9876.00,
        2221.10,
        'active',
        '2025-02-20 16:15:00'
    ),
    (
        'Ana Costa',
        'ana.costa@email.com',
        '(11) 99999-0004',
        '789.123.456-04',
        'AC2025004',
        'bronze',
        25.00,
        'ana.costa@email.com',
        'email',
        7654.00,
        1530.80,
        'active',
        '2025-03-05 09:45:00'
    ),
    (
        'Pedro Oliveira',
        'pedro.oliveira@email.com',
        '(11) 99999-0005',
        '321.654.987-05',
        'PO2025005',
        'bronze',
        25.00,
        '321.654.987-05',
        'cpf',
        6543.00,
        1308.60,
        'active',
        '2025-03-12 11:20:00'
    ),
    (
        'Lucia Fernandes',
        'lucia.fernandes@email.com',
        '(11) 99999-0006',
        '654.321.789-06',
        'LF2025006',
        'affiliate',
        20.00,
        'lucia.fernandes@email.com',
        'email',
        3210.00,
        481.50,
        'active',
        '2025-04-01 13:00:00'
    ),
    (
        'Roberto Lima',
        'roberto.lima@email.com',
        '(11) 99999-0007',
        '147.258.369-07',
        'RL2025007',
        'affiliate',
        20.00,
        '+5511999990007',
        'phone',
        2890.00,
        433.50,
        'active',
        '2025-04-15 15:30:00'
    ),
    (
        'Sandra Alves',
        'sandra.alves@email.com',
        '(11) 99999-0008',
        '258.369.147-08',
        'SA2025008',
        'pending_approval',
        20.00,
        'sandra.alves@email.com',
        'email',
        0.00,
        0.00,
        'pending_approval',
        NULL
    );

-- ===== VENDAS DOS AFILIADOS =====
INSERT INTO
    affiliate_sales (
        affiliate_id,
        merchant_id,
        subscription_id,
        plan_code,
        billing_cycle,
        sale_amount,
        commission_rate,
        commission_amount,
        status,
        confirmed_at,
        customer_name,
        customer_email,
        customer_phone
    )
VALUES
    -- Vendas do João Silva (gold)
    (
        1,
        1,
        1,
        'premium',
        'monthly',
        197.00,
        35.00,
        68.95,
        'confirmed',
        '2025-07-15 09:00:00',
        'Pizzaria do João',
        'joao@pizzariadojoao.com',
        '(11) 99999-1234'
    ),
    (
        1,
        3,
        3,
        'enterprise',
        'yearly',
        3970.00,
        35.00,
        1389.50,
        'confirmed',
        '2024-08-30 14:00:00',
        'Empresa ABC',
        'admin@abccorp.com',
        '(11) 77777-9012'
    ),

-- Vendas da Maria Santos (silver)
(
    2,
    5,
    5,
    'basic',
    'monthly',
    97.00,
    30.00,
    29.10,
    'confirmed',
    '2025-07-10 11:00:00',
    'Mercado Central',
    'gerencia@mercadocentral.com',
    '(11) 55555-7890'
),

-- Vendas do Carlos Pereira (silver)
(
    3,
    4,
    4,
    'premium',
    'monthly',
    197.00,
    30.00,
    59.10,
    'confirmed',
    '2025-06-05 08:00:00',
    'Burguer House',
    'contato@burguerhouse.com',
    '(11) 66666-3456'
);

-- ===== TRANSAÇÕES DE PAGAMENTO =====
INSERT INTO
    payment_transactions (
        transaction_code,
        source_type,
        source_id,
        amount_original,
        amount_final,
        payment_method,
        gateway_provider,
        gateway_transaction_id,
        status,
        customer_name,
        customer_email,
        processed_at,
        approved_at,
        description
    )
VALUES
    -- Renovação Pizzaria do João
    (
        'TXN_SUB_20250815_001',
        'subscription_renewal',
        1,
        197.00,
        197.00,
        'credit_card',
        'safe2pay',
        'S2P_78945612301',
        'approved',
        'Pizzaria do João',
        'joao@pizzariadojoao.com',
        '2025-08-02 06:45:00',
        '2025-08-02 06:45:30',
        'Renovação mensal Plano Premium'
    ),

-- Nova assinatura Loja da Maria (trial)
(
    'TXN_SUB_20250801_002',
    'subscription_new',
    2,
    97.00,
    0.00,
    'credit_card',
    'safe2pay',
    'S2P_78945612302',
    'approved',
    'Loja da Maria',
    'maria@lojadamaria.com',
    '2025-08-01 10:15:00',
    '2025-08-01 10:15:45',
    'Período trial Plano Básico'
),

-- Renovação anual Empresa ABC
(
    'TXN_SUB_20240830_003',
    'subscription_renewal',
    3,
    3970.00,
    3970.00,
    'bank_slip',
    'safe2pay',
    'S2P_78945612303',
    'approved',
    'Empresa ABC',
    'admin@abccorp.com',
    '2024-08-30 14:30:00',
    '2024-08-30 14:35:20',
    'Renovação anual Plano Enterprise'
),

-- Falha na renovação Burguer House
(
    'TXN_SUB_20250805_004',
    'subscription_renewal',
    4,
    197.00,
    197.00,
    'credit_card',
    'safe2pay',
    'S2P_78945612304',
    'declined',
    'Burguer House',
    'contato@burguerhouse.com',
    '2025-08-05 08:15:00',
    NULL,
    'Tentativa renovação - cartão recusado'
),

-- Renovação Mercado Central
(
    'TXN_SUB_20250810_005',
    'subscription_renewal',
    5,
    97.00,
    97.00,
    'pix',
    'safe2pay',
    'S2P_78945612305',
    'approved',
    'Mercado Central',
    'gerencia@mercadocentral.com',
    '2025-08-02 07:02:00',
    '2025-08-02 07:02:15',
    'Renovação mensal Plano Básico'
);

-- ===== COMISSÕES DOS AFILIADOS =====
INSERT INTO
    affiliate_commissions (
        affiliate_id,
        sale_id,
        amount,
        rate_applied,
        reference_period,
        status,
        approved_at,
        approved_by
    )
VALUES
    -- João Silva
    (
        1,
        1,
        68.95,
        35.00,
        '2025-07',
        'approved',
        '2025-08-01 10:00:00',
        1
    ),
    (
        1,
        2,
        1389.50,
        35.00,
        '2024-08',
        'paid',
        '2024-09-01 10:00:00',
        1
    ),

-- Maria Santos
(
    2,
    3,
    29.10,
    30.00,
    '2025-07',
    'approved',
    '2025-08-01 10:00:00',
    1
),

-- Carlos Pereira
( 3, 4, 59.10, 30.00, '2025-06', 'paid', '2025-07-01 10:00:00', 1 );

-- ===== EVENTOS DE PAGAMENTO =====
INSERT INTO
    payment_events (
        transaction_id,
        event_type,
        previous_status,
        new_status,
        triggered_by,
        event_data
    )
VALUES (
        1,
        'created',
        NULL,
        'draft',
        'system',
        '{"created_by": "subscription_renewal_job"}'
    ),
    (
        1,
        'sent_to_gateway',
        'draft',
        'pending',
        'system',
        '{"gateway": "safe2pay", "amount": 197.00}'
    ),
    (
        1,
        'gateway_response',
        'pending',
        'approved',
        'gateway',
        '{"gateway_transaction_id": "S2P_78945612301", "response_time": "500ms"}'
    ),
    (
        1,
        'payment_approved',
        'approved',
        'approved',
        'webhook',
        '{"webhook_id": "wh_123456", "processed_at": "2025-08-02T06:45:30Z"}'
    ),
    (
        2,
        'created',
        NULL,
        'draft',
        'system',
        '{"created_by": "new_subscription"}'
    ),
    (
        2,
        'sent_to_gateway',
        'draft',
        'pending',
        'system',
        '{"gateway": "safe2pay", "amount": 0.00, "trial": true}'
    ),
    (
        2,
        'payment_approved',
        'pending',
        'approved',
        'system',
        '{"trial_period": "7_days"}'
    ),
    (
        4,
        'created',
        NULL,
        'draft',
        'system',
        '{"created_by": "subscription_renewal_job"}'
    ),
    (
        4,
        'sent_to_gateway',
        'draft',
        'pending',
        'system',
        '{"gateway": "safe2pay", "amount": 197.00}'
    ),
    (
        4,
        'gateway_response',
        'pending',
        'declined',
        'gateway',
        '{"error_code": "insufficient_funds", "gateway_message": "Cartão sem saldo"}'
    ),
    (
        4,
        'payment_declined',
        'declined',
        'declined',
        'webhook',
        '{"webhook_id": "wh_123459", "retry_scheduled": true}'
    );