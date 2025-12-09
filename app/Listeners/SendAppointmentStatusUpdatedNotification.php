<?php

namespace App\Listeners;

use App\Events\AppointmentStatusUpdated;
use App\Notifications\AppointmentStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAppointmentStatusUpdatedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(AppointmentStatusUpdated $event): void
    {
        $appointment = $event->appointment;
        $patientUser = $appointment->patient->user;
        $doctorUser = $appointment->doctor->user;

        $doctorUser->notify(new AppointmentStatusUpdatedNotification($appointment));

        $patientUser->notify(new AppointmentStatusUpdatedNotification($appointment));
    }
}
