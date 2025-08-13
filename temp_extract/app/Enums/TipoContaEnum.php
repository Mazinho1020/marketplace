<?php

namespace App\Enums;

enum TipoContaEnum: string
{
    case RECEITA = 'receita';
    case DESPESA = 'despesa';

    public function label(): string
    {
        return match($this) {
            self::RECEITA => 'Receita',
            self::DESPESA => 'Despesa',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::RECEITA => 'success',
            self::DESPESA => 'danger',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::RECEITA => 'arrow-up-circle',
            self::DESPESA => 'arrow-down-circle',
        };
    }
}