<?php

namespace App\Enums\Payment;

enum PaymentStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case APPROVED = 'approved';
    case DECLINED = 'declined';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Rascunho',
            self::PENDING => 'Aguardando',
            self::PROCESSING => 'Processando',
            self::APPROVED => 'Aprovado',
            self::DECLINED => 'Negado',
            self::CANCELLED => 'Cancelado',
            self::REFUNDED => 'Estornado',
            self::EXPIRED => 'Expirado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::APPROVED => 'success',
            self::DECLINED => 'danger',
            self::CANCELLED => 'dark',
            self::REFUNDED => 'warning',
            self::EXPIRED => 'danger',
        };
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function isDeclined(): bool
    {
        return $this === self::DECLINED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isRefunded(): bool
    {
        return $this === self::REFUNDED;
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::APPROVED,
            self::DECLINED,
            self::CANCELLED,
            self::REFUNDED,
            self::EXPIRED
        ]);
    }
}
