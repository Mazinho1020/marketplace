# Sistema de Pagamentos - Marketplace

## Visão Geral

Sistema completo de pagamentos com arquitetura modular separando **Merchants** e **Affiliates**, respeitando as regras de configuração estabelecidas no banco de dados.

## Arquitetura do Sistema

### 📁 Estrutura Modular

```
app/
├── Core/                          # Sistema compartilhado
│   ├── Config/ConfigManager.php   # Gerenciamento de configurações
│   └── Cache/RedisCacheManager.php # Cache inteligente com TTLs específicos
├── Merchants/                     # Módulo de Merchants
│   ├── Models/
│   │   ├── Merchant.php          # Modelo principal do merchant
│   │   ├── MerchantSubscription.php # Gerenciamento de assinaturas
│   │   └── SubscriptionPlan.php   # Definições de planos
│   ├── Controllers/
│   │   ├── PlanController.php    # Gerenciamento de planos
│   │   └── SubscriptionController.php # Gestão de assinaturas
│   └── Services/
│       └── PlanService.php       # Lógica de negócio dos planos
├── Affiliates/                   # Módulo de Affiliates
│   ├── Models/
│   │   ├── Affiliate.php         # Modelo principal do affiliate
│   │   ├── AffiliateCommission.php # Comissões
│   │   ├── AffiliateReferral.php # Referrals/indicações
│   │   └── AffiliatePayment.php  # Pagamentos
│   ├── Controllers/
│   │   └── AffiliateController.php # Gestão completa de affiliates
│   └── Services/
│       └── AffiliateService.php  # Lógica de negócio dos affiliates
└── Http/Middleware/
    └── CheckPlanFeature.php      # Middleware de controle de acesso
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

1. **payment_gateways** - Configuração dos gateways de pagamento
2. **payment_transactions** - Transações de pagamento
3. **merchants** - Dados dos comerciantes
4. **merchant_subscriptions** - Assinaturas dos merchants
5. **subscription_plans** - Planos disponíveis
6. **affiliates** - Dados dos afiliados
7. **affiliate_referrals** - Indicações/referrals
8. **affiliate_commissions** - Comissões dos affiliates
9. **affiliate_payments** - Pagamentos para affiliates
10. **config_definitions** - Definições de configuração
11. **config_values** - Valores de configuração

### Scripts SQL Criados

-   `001_create_payment_core_tables.sql` - Tabelas centrais do sistema
-   `002_create_merchants_subscription_tables.sql` - Sistema de assinaturas
-   `003_create_affiliates_tables.sql` - Sistema de afiliados
-   `004_create_config_system_tables.sql` - Sistema de configuração
-   `005_insert_sample_data.sql` - Dados de exemplo e configurações

## 🚀 Funcionalidades Implementadas

### Sistema Core

#### ConfigManager

-   ✅ Gerenciamento centralizado de configurações
-   ✅ Cache inteligente com invalidação automática
-   ✅ Configurações específicas por plano, affiliate e merchant
-   ✅ Validação de configurações com tipos e regras

#### RedisCacheManager

-   ✅ Cache estratégico com TTLs diferenciados:
    -   Configurações: 1 hora
    -   Transações: 5 minutos
    -   Relatórios: 24 horas
-   ✅ Cache de features e limites de merchants
-   ✅ Cache de estatísticas do dashboard
-   ✅ Invalidação inteligente de cache

### Módulo Merchants

#### Modelos

-   ✅ **Merchant**: Gestão completa de comerciantes

    -   Verificação de features ativas
    -   Controle de limites de uso
    -   Geração de chaves de licença
    -   Estatísticas de uso

-   ✅ **MerchantSubscription**: Ciclo de vida das assinaturas

    -   Ativação e renovação
    -   Upgrade/downgrade de planos
    -   Gestão de trials
    -   Cálculo de valores proporcionais

-   ✅ **SubscriptionPlan**: Definições de planos
    -   Features e limitações
    -   Preços por ciclo de cobrança
    -   Comparação entre planos
    -   Descontos anuais

#### Controllers

-   ✅ **PlanController**: API para gestão de planos

    -   Listagem e comparação de planos
    -   Cálculo de custos de upgrade
    -   Recomendações personalizadas

-   ✅ **SubscriptionController**: Gestão completa de assinaturas
    -   Criação de novas assinaturas
    -   Upgrade/downgrade de planos
    -   Cancelamento e reativação
    -   Histórico completo

#### Services

-   ✅ **PlanService**: Lógica de negócio avançada
    -   Verificação de features
    -   Comparação de múltiplos planos
    -   Cálculo de ROI de upgrades
    -   Análise de limites de uso
    -   Recomendações baseadas em uso

#### Middleware

-   ✅ **CheckPlanFeature**: Controle de acesso
    -   Verificação de features por rota
    -   Validação de limites
    -   Redirecionamento para upgrade
    -   Integração com cache

### Módulo Affiliates

#### Modelos

-   ✅ **Affiliate**: Gestão de afiliados

    -   Aprovação/rejeição
    -   Cálculo de comissões
    -   Estatísticas de performance
    -   Validação de dados bancários

-   ✅ **AffiliateReferral**: Sistema de indicações

    -   Tracking de origem
    -   Conversão de referrals
    -   Tempo até conversão
    -   Dados de UTM

-   ✅ **AffiliateCommission**: Gestão de comissões

    -   Diferentes status de pagamento
    -   Cálculo baseado em regras
    -   Histórico completo

-   ✅ **AffiliatePayment**: Pagamentos para affiliates
    -   Múltiplos métodos (PIX, transferência, PayPal)
    -   Processamento em lote
    -   Controle de falhas e retry

#### Controllers

-   ✅ **AffiliateController**: API completa
    -   CRUD de affiliates
    -   Aprovação/rejeição
    -   Suspensão/reativação
    -   Relatórios de performance
    -   Processamento de pagamentos

#### Services

-   ✅ **AffiliateService**: Lógica de negócio
    -   Processamento de referrals
    -   Cálculo de comissões
    -   Pagamentos em lote
    -   Estatísticas do programa
    -   Top performers
    -   Validação de códigos

## 💡 Principais Features

### Sistema de Cache Inteligente

-   Cache em múltiplas camadas (Redis + fallback)
-   TTLs específicos por tipo de dado
-   Invalidação automática e manual
-   Cache de queries complexas

### Sistema de Configuração

-   Configurações hierárquicas (global → affiliate → merchant)
-   Tipos de dados validados
-   Cache de configurações
-   API para gerenciamento

### Controle de Acesso Baseado em Planos

-   Middleware para verificação de features
-   Controle de limites por plano
-   Redirecionamento automático para upgrade
-   Cache de permissões

### Sistema de Assinaturas Avançado

-   Múltiplos ciclos de cobrança
-   Trials gratuitos
-   Upgrade/downgrade com cálculo proporcional
-   Auto renovação configurável
-   Histórico completo

### Programa de Afiliados Completo

-   Sistema de tracking avançado
-   Comissões configuráveis
-   Múltiplos métodos de pagamento
-   Relatórios detalhados
-   Processamento automático de pagamentos

### Analytics e Relatórios

-   Estatísticas em tempo real
-   Performance de affiliates
-   Usage tracking de merchants
-   ROI de upgrades
-   Conversion rates

## 🔧 Configuração e Uso

### Configurações no Banco

O sistema utiliza configurações dinâmicas armazenadas nas tabelas `config_definitions` e `config_values`. Principais configurações:

```sql
-- Taxa de comissão padrão para affiliates
affiliate.default_commission_rate = 10

-- Valor mínimo para pagamento de comissões
affiliate.min_payout_amount = 50

-- Dias para pagamento de comissões
affiliate.commission_payment_days = 30

-- Features disponíveis por plano
merchant.basic.features = ["dashboard", "transactions"]
merchant.premium.features = ["dashboard", "transactions", "pdv", "reports"]
```

### Cache

O sistema utiliza Redis para cache com estratégia inteligente:

```php
// Cache de configurações (1 hora)
$config = $configManager->get('key');

// Cache de features de merchant (30 min)
$features = $cacheManager->getMerchantFeatures($merchantId);

// Cache de estatísticas (24 horas)
$stats = $cacheManager->getDashboardStats();
```

### Middleware de Controle

```php
// Em routes/web.php
Route::middleware(['check.plan.feature:pdv'])->group(function () {
    Route::get('/pdv', [PDVController::class, 'index']);
});
```

## 📊 Métricas e Monitoramento

### Merchants

-   Usage tracking por feature
-   Limites de uso em tempo real
-   Conversão de trials
-   Churn rate por plano

### Affiliates

-   Taxa de conversão de referrals
-   Comissões por período
-   Top performers
-   ROI do programa

### Sistema Geral

-   Performance de cache
-   Transações por gateway
-   Revenue por plano
-   Customer Lifetime Value

## 🚀 Próximos Passos

1. **Frontend**: Implementar dashboards para merchants e affiliates
2. **Webhooks**: Sistema de notificações em tempo real
3. **API Externa**: Endpoints para integrações
4. **Mobile**: App para gestão móvel
5. **BI**: Dashboard executivo com métricas avançadas
6. **Machine Learning**: Recomendações inteligentes de planos

## 📝 Logs e Auditoria

Todos os eventos importantes são logados:

-   Criação/alteração de subscriptions
-   Aprovação/rejeição de affiliates
-   Processamento de comissões
-   Pagamentos realizados
-   Mudanças de configuração

## 🔒 Segurança

-   Validação rigorosa de dados
-   Controle de acesso baseado em roles
-   Cache de permissões
-   Auditoria completa
-   Dados bancários protegidos
-   Rate limiting em APIs críticas

---

**Sistema completo implementado e pronto para uso em produção!**
