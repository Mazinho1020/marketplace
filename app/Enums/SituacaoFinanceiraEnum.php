<?php

namespace App\Enums;

enum SituacaoFinanceiraEnum: string
{
    case PENDENTE = 'pendente';
    case PAGO = 'pago';
    case VENCIDO = 'vencido';
    case CANCELADO = 'cancelado';
    case EM_NEGOCIACAO = 'em_negociacao';

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::PAGO => 'Pago',
            self::VENCIDO => 'Vencido',
            self::CANCELADO => 'Cancelado',
            self::EM_NEGOCIACAO => 'Em Negociação',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDENTE => 'warning',
            self::PAGO => 'success',
            self::VENCIDO => 'danger',
            self::CANCELADO => 'secondary',
            self::EM_NEGOCIACAO => 'info',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDENTE => 'fa-clock',
            self::PAGO => 'fa-check-circle',
            self::VENCIDO => 'fa-exclamation-triangle',
            self::CANCELADO => 'fa-times-circle',
            self::EM_NEGOCIACAO => 'fa-handshake',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'color' => $case->color()
            ],
            self::cases()
        );
    }

    public function isPago(): bool
    {
        return $this === self::PAGO;
    }

    public function isVencido(): bool
    {
        return $this === self::VENCIDO;
    }

    public function isPendente(): bool
    {
        return $this === self::PENDENTE;
    }
}
