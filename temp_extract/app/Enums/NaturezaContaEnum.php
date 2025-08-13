<?php

namespace App\Enums;

enum NaturezaContaEnum: string
{
    case DEBITO = 'debito';
    case CREDITO = 'credito';

    public function label(): string
    {
        return match($this) {
            self::DEBITO => 'Débito',
            self::CREDITO => 'Crédito',
        };
    }

    public function sinal(): int
    {
        return match($this) {
            self::DEBITO => 1,
            self::CREDITO => -1,
        };
    }
}