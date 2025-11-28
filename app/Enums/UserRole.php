<?php

namespace App\Enums;

use ValueError;

enum UserRole: int
{
    case PATIENT = 1;
    case DOCTOR = 2;

    public function label(): string
    {
        return match ($this) {
            self::PATIENT => 'patient',
            self::DOCTOR => 'doctor',
        };
    }

    public static function fromLabel(string $label): self
    {
        return match ($label) {
            'patient' => self::PATIENT,
            'doctor' => self::DOCTOR,
            default => throw new ValueError('Invalid role label: '.$label),
        };
    }
}
