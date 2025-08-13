<?php

namespace App\Enums;

enum TipoContaEnum: string
{
    case RECEITA = 'receita';
    case DESPESA = 'despesa';
    case CUSTO = 'custo';
    case ATIVO = 'ativo';
    case PASSIVO = 'passivo';
    case PATRIMONIO = 'patrimonio';

    public function label(): string
    {
        return match ($this) {
            self::RECEITA => 'Receita',
            self::DESPESA => 'Despesa',
            self::CUSTO => 'Custo',
            self::ATIVO => 'Ativo',
            self::PASSIVO => 'Passivo',
            self::PATRIMONIO => 'Patrimônio',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RECEITA => 'success',
            self::DESPESA => 'danger',
            self::CUSTO => 'warning',
            self::ATIVO => 'info',
            self::PASSIVO => 'secondary',
            self::PATRIMONIO => 'primary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::RECEITA => 'arrow-up-circle',
            self::DESPESA => 'arrow-down-circle',
            self::CUSTO => 'minus-circle',
            self::ATIVO => 'plus-square',
            self::PASSIVO => 'minus-square',
            self::PATRIMONIO => 'shield',
        };
    }

    public function naturezaPadrao(): NaturezaContaEnum
    {
        return match ($this) {
            self::RECEITA => NaturezaContaEnum::CREDITO,
            self::DESPESA, self::CUSTO, self::ATIVO => NaturezaContaEnum::DEBITO,
            self::PASSIVO, self::PATRIMONIO => NaturezaContaEnum::CREDITO,
        };
    }
}
