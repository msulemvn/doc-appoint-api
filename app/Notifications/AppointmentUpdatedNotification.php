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
        $notificationId = $notifiable->notifications()
            ->where('type', static::class)
            ->where('data->appointment_id', $this->appointment->id)
            ->latest()
            ->first()?->id ?? uniqid();

        return new BroadcastMessage([
            'id' => $notificationId,
            'type' => static::class,
            'data' => [
                'appointment_id' => $this->appointment->id,
                'patient_name' => $this->appointment->patient->user->name,
                'doctor_name' => $this->appointment->doctor->user->name,
                'appointment_date' => $this->appointment->appointment_date->toDateTimeString(),
                'status' => $this->appointment->status->value,
                'notes' => $this->appointment->notes,
            ],
            'read_at' => null,
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ]);
    }

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
