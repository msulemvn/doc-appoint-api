<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AppointmentUpdatedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->user->name,
            'doctor_name' => $this->appointment->doctor->user->name,
            'appointment_date' => $this->appointment->appointment_date->toDateTimeString(),
            'status' => $this->appointment->status->value,
            'notes' => $this->appointment->notes,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $appointment = $this->appointment->load(['patient.user', 'doctor.user']);

        return new BroadcastMessage([
            'appointment' => $appointment->toArray(),
        ]);
    }

    public function broadcastAs(): string
    {
        return 'appointment.status.updated';
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
