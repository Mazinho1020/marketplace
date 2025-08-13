<?php

namespace App\Enums;

enum SyncStatusEnum: string
{
    case PENDENTE = 'pendente';
    case SINCRONIZADO = 'sincronizado';
    case ERRO = 'erro';

    public function label(): string
    {
        return match($this) {
            self::PENDENTE => 'Pendente',
            self::SINCRONIZADO => 'Sincronizado',
            self::ERRO => 'Erro',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDENTE => 'warning',
            self::SINCRONIZADO => 'success',
            self::ERRO => 'danger',
        };
    }
}