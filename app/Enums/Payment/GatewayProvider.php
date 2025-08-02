<?php

namespace App\Enums\Payment;

enum GatewayProvider: string
{
    case SAFE2PAY = 'safe2pay';
    case MERCADOPAGO = 'mercadopago';
    case PAGSEGURO = 'pagseguro';
    case STRIPE = 'stripe';
    case OUTROS = 'outros';

    public function label(): string
    {
        return match ($this) {
            self::SAFE2PAY => 'Safe2Pay',
            self::MERCADOPAGO => 'Mercado Pago',
            self::PAGSEGURO => 'PagSeguro',
            self::STRIPE => 'Stripe',
            self::OUTROS => 'Outros',
        };
    }

    public function website(): string
    {
        return match ($this) {
            self::SAFE2PAY => 'https://safe2pay.com.br',
            self::MERCADOPAGO => 'https://mercadopago.com.br',
            self::PAGSEGURO => 'https://pagseguro.uol.com.br',
            self::STRIPE => 'https://stripe.com',
            self::OUTROS => '',
        };
    }

    public function supportedMethods(): array
    {
        return match ($this) {
            self::SAFE2PAY => [
                PaymentMethod::PIX,
                PaymentMethod::CREDIT_CARD,
                PaymentMethod::DEBIT_CARD,
                PaymentMethod::BANK_SLIP,
            ],
            self::MERCADOPAGO => [
                PaymentMethod::PIX,
                PaymentMethod::CREDIT_CARD,
                PaymentMethod::DEBIT_CARD,
                PaymentMethod::BANK_SLIP,
            ],
            self::PAGSEGURO => [
                PaymentMethod::PIX,
                PaymentMethod::CREDIT_CARD,
                PaymentMethod::DEBIT_CARD,
                PaymentMethod::BANK_SLIP,
            ],
            self::STRIPE => [
                PaymentMethod::CREDIT_CARD,
                PaymentMethod::DEBIT_CARD,
            ],
            self::OUTROS => [],
        };
    }

    public function hasWebhookSupport(): bool
    {
        return match ($this) {
            self::SAFE2PAY, self::MERCADOPAGO, self::PAGSEGURO, self::STRIPE => true,
            self::OUTROS => false,
        };
    }

    public function environment(): array
    {
        return match ($this) {
            self::SAFE2PAY => ['sandbox', 'production'],
            self::MERCADOPAGO => ['sandbox', 'production'],
            self::PAGSEGURO => ['sandbox', 'production'],
            self::STRIPE => ['test', 'live'],
            self::OUTROS => ['test', 'production'],
        };
    }
}
