<?php

namespace App\Enums\Payment;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case PIX = 'pix';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case BANK_SLIP = 'bank_slip';
    case BANK_TRANSFER = 'bank_transfer';
    case CRYPTO = 'crypto';
    case DIGITAL_WALLET = 'digital_wallet';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Dinheiro',
            self::PIX => 'PIX',
            self::CREDIT_CARD => 'Cartão de Crédito',
            self::DEBIT_CARD => 'Cartão de Débito',
            self::BANK_SLIP => 'Boleto',
            self::BANK_TRANSFER => 'Transferência',
            self::CRYPTO => 'Criptomoeda',
            self::DIGITAL_WALLET => 'Carteira Digital',
        };
    }

    public function requiresGateway(): bool
    {
        return match ($this) {
            self::CASH => false,
            default => true,
        };
    }

    public function isOnline(): bool
    {
        return $this->requiresGateway();
    }

    public function isOffline(): bool
    {
        return !$this->requiresGateway();
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH => 'fas fa-money-bill',
            self::PIX => 'fas fa-qrcode',
            self::CREDIT_CARD => 'fas fa-credit-card',
            self::DEBIT_CARD => 'fas fa-credit-card',
            self::BANK_SLIP => 'fas fa-barcode',
            self::BANK_TRANSFER => 'fas fa-university',
            self::CRYPTO => 'fab fa-bitcoin',
            self::DIGITAL_WALLET => 'fas fa-wallet',
        };
    }

    public function allowsInstallments(): bool
    {
        return match ($this) {
            self::CREDIT_CARD => true,
            default => false,
        };
    }
}
