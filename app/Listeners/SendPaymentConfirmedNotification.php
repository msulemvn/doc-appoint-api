<?php

namespace App\Listeners;

use App\Events\PaymentConfirmed;
use App\Notifications\PaymentConfirmedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentConfirmedNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(PaymentConfirmed $event): void
    {
        $appointment = $event->appointment;
        $patientUser = $appointment->patient->user;
        $doctorUser = $appointment->doctor->user;

        $patientUser->notify(new PaymentConfirmedNotification($appointment));
        $doctorUser->notify(new PaymentConfirmedNotification($appointment));
    }
}
