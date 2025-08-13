<?php

namespace App\Enums;

enum NaturezaContaEnum: string
{
    case DEBITO = 'D';
    case CREDITO = 'C';

    public function label(): string
    {
        return match ($this) {
            self::DEBITO => 'Débito',
            self::CREDITO => 'Crédito',
        };
    }

    public function sinal(): int
    {
        return match ($this) {
            self::DEBITO => 1,
            self::CREDITO => -1,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DEBITO => 'danger',
            self::CREDITO => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DEBITO => 'minus-circle',
            self::CREDITO => 'plus-circle',
        };
    }
}
