<?php

namespace App\Actions\Appointment;

use App\Enums\AppointmentStatus;
use App\Events\AppointmentStatusUpdated;
use App\Models\Appointment;

class UpdateAppointmentStatusAction
{
    public function execute(Appointment $appointment, string $status): Appointment
    {
        $appointment->update([
            'status' => AppointmentStatus::from($status),
        ]);

        $appointment->load(['doctor', 'patient']);

        AppointmentStatusUpdated::dispatch($appointment);

        return $appointment;
    }
}
