<?php

namespace App\Enums;

use ValueError;

enum AppointmentStatus: int
{
    case PENDING = 0;
    case CONFIRMED = 1;
    case CANCELLED = 2;
    case COMPLETED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'pending',
            self::CONFIRMED => 'confirmed',
            self::CANCELLED => 'cancelled',
            self::COMPLETED => 'completed',
        };
    }

    public static function fromLabel(string $label): self
    {
        return match ($label) {
            'pending' => self::PENDING,
            'confirmed' => self::CONFIRMED,
            'cancelled' => self::CANCELLED,
            'completed' => self::COMPLETED,
            default => throw new ValueError('Invalid status label: '.$label),
        };
    }
}
