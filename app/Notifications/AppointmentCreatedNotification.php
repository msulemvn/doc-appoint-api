<?php

namespace App\Notifications;

use Illuminate\Broadcasting\Channel;
use App\Models\Appointment;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class AppointmentCreatedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Appointment $appointment) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
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

    /**
     * Get the array representation of the notification for broadcasting.
     *
     * @return array<string, mixed>
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'type' => 'appointment_created',
            'appointment_id' => $this->appointment->id,
            'patient_name' => $this->appointment->patient->user->name,
            'doctor_name' => $this->appointment->doctor->user->name,
            'appointment_date' => $this->appointment->appointment_date->toDateTimeString(),
            'status' => $this->appointment->status->value,
            'notes' => $this->appointment->notes,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->appointment->patient->user_id),
            new PrivateChannel('users.'.$this->appointment->doctor->user_id),
        ];
    }

    /**
     * The type of event that is being broadcast.
     */
    public function broadcastAs(): string
    {
        return 'appointment.created';
    }
}
