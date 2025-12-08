<?php

namespace App\Listeners;

use App\Events\AppointmentUpdated;
use App\Notifications\AppointmentUpdatedNotification;

class SendAppointmentUpdatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(AppointmentUpdated $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient;
        $doctor = $appointment->doctor;

        $patient->notify(new AppointmentUpdatedNotification($appointment));
        $doctor->notify(new AppointmentUpdatedNotification($appointment));
    }
}
