<?php

namespace App\Enums;

enum NaturezaFinanceiraEnum: string
{
    case PAGAR = 'saida';
    case RECEBER = 'entrada';

    public function label(): string
    {
        return match ($this) {
            self::PAGAR => 'Conta a Pagar',
            self::RECEBER => 'Conta a Receber',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PAGAR => 'danger',
            self::RECEBER => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PAGAR => 'fa-money-bill-trend-up',
            self::RECEBER => 'fa-hand-holding-dollar',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
