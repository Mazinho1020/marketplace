<?php

namespace App\Enums\Payment;

enum SourceType: string
{
    case PDV = 'pdv';
    case LANCAMENTO = 'lancamento';
    case SITE_CLIENTE = 'site_cliente';
    case SITE_PLANOS = 'site_planos';
    case MARKETPLACE = 'marketplace';
    case API_EXTERNA = 'api_externa';
    case MOBILE_APP = 'mobile_app';
    case WEBHOOK = 'webhook';
    case DELIVERY = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::PDV => 'PDV',
            self::LANCAMENTO => 'Lançamento',
            self::SITE_CLIENTE => 'Site Cliente',
            self::SITE_PLANOS => 'Site Planos',
            self::MARKETPLACE => 'Marketplace',
            self::API_EXTERNA => 'API Externa',
            self::MOBILE_APP => 'App Mobile',
            self::WEBHOOK => 'Webhook',
            self::DELIVERY => 'Delivery',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PDV => 'Venda no Ponto de Venda',
            self::LANCAMENTO => 'Pagamento de Lançamento (conta a pagar/receber)',
            self::SITE_CLIENTE => 'Pagamento via site do cliente',
            self::SITE_PLANOS => 'Pagamento de plano/assinatura',
            self::MARKETPLACE => 'Venda no marketplace',
            self::API_EXTERNA => 'Pagamento via API externa',
            self::MOBILE_APP => 'Pagamento via aplicativo mobile',
            self::WEBHOOK => 'Processamento via webhook',
            self::DELIVERY => 'Pagamento de delivery',
        };
    }

    public function allowsOfflinePayment(): bool
    {
        return match ($this) {
            self::PDV, self::DELIVERY => true,
            default => false,
        };
    }

    public function requiresCustomer(): bool
    {
        return match ($this) {
            self::SITE_CLIENTE, self::SITE_PLANOS, self::DELIVERY => true,
            default => false,
        };
    }
}
