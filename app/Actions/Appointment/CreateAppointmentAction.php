<?php

namespace App\Actions\Appointment;

use App\Enums\AppointmentStatus;
use App\Events\AppointmentCreated;
use App\Models\Appointment;
use App\Models\PatientDetail;

class CreateAppointmentAction
{
    public function execute(PatientDetail $patient, array $data): Appointment
    {
        $appointment = $patient->appointments()->create([
            'doctor_id' => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'notes' => $data['notes'] ?? null,
            'status' => AppointmentStatus::PENDING,
        ]);

        event(new AppointmentCreated($appointment));

        return $appointment->load('doctor');
    }
}
