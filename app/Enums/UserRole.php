<?php

namespace App\Enums;

enum UserRole: string
{
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';

    public function label(): string
    {
        return match ($this) {
            self::PATIENT => 'Patient',
            self::DOCTOR => 'Doctor',
        };
    }
}
