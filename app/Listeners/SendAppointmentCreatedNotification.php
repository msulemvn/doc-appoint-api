<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Notifications\AppointmentCreatedNotification;

class SendAppointmentCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AppointmentCreated $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;
        $doctor = $appointment->doctor;

        $patient->notify(new AppointmentCreatedNotification($appointment));
        $doctor->notify(new AppointmentCreatedNotification($appointment));
    }
}
