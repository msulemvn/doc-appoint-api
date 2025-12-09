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
        $patientUser = $appointment->patient->user;
        $doctorUser = $appointment->doctor->user;

        $patientUser->notify(new AppointmentCreatedNotification($appointment));
        $doctorUser->notify(new AppointmentCreatedNotification($appointment));
    }
}
