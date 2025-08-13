<?php

namespace App\Enums;

enum FrequenciaRecorrenciaEnum: string
{
    case SEMANAL = 'semanal';
    case QUINZENAL = 'quinzenal';
    case MENSAL = 'mensal';
    case BIMESTRAL = 'bimestral';
    case TRIMESTRAL = 'trimestral';
    case SEMESTRAL = 'semestral';
    case ANUAL = 'anual';

    public function label(): string
    {
        return match ($this) {
            self::SEMANAL => 'Semanal',
            self::QUINZENAL => 'Quinzenal',
            self::MENSAL => 'Mensal',
            self::BIMESTRAL => 'Bimestral',
            self::TRIMESTRAL => 'Trimestral',
            self::SEMESTRAL => 'Semestral',
            self::ANUAL => 'Anual',
        };
    }

    public function dias(): int
    {
        return match ($this) {
            self::SEMANAL => 7,
            self::QUINZENAL => 15,
            self::MENSAL => 30,
            self::BIMESTRAL => 60,
            self::TRIMESTRAL => 90,
            self::SEMESTRAL => 180,
            self::ANUAL => 365,
        };
    }

    public static function options(): array
    {
        return array_map(
            fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'dias' => $case->dias()
            ],
            self::cases()
        );
    }

    public function calcularProximaData(\Carbon\Carbon $dataBase): \Carbon\Carbon
    {
        return match ($this) {
            self::SEMANAL => $dataBase->addWeek(),
            self::QUINZENAL => $dataBase->addDays(15),
            self::MENSAL => $dataBase->addMonth(),
            self::BIMESTRAL => $dataBase->addMonths(2),
            self::TRIMESTRAL => $dataBase->addMonths(3),
            self::SEMESTRAL => $dataBase->addMonths(6),
            self::ANUAL => $dataBase->addYear(),
        };
    }
}
