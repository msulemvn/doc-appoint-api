<?php

namespace App\Actions\Appointment;

use App\Enums\AppointmentStatus;
use App\Events\AppointmentUpdated;
use App\Models\Appointment;

class UpdateAppointmentStatusAction
{
    public function execute(Appointment $appointment, string $status): Appointment
    {
        $appointment->update([
            'status' => AppointmentStatus::from($status),
        ]);

        $appointment->load(['doctor', 'patient']);

        AppointmentUpdated::dispatch($appointment);

        return $appointment;
    }
}
