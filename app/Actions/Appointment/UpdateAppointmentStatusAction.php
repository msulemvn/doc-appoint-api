<?php

namespace App\Actions\Appointment;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;

class UpdateAppointmentStatusAction
{
    public function execute(Appointment $appointment, string $status): Appointment
    {
        $appointment->update([
            'status' => AppointmentStatus::from($status),
        ]);

        return $appointment->load(['doctor', 'patient']);
    }
}
