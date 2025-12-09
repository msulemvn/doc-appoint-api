<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusUpdatedNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
        $this->onQueue('notification');
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        $isPatient = $notifiable->role === 'patient';
        $doctorName = $this->appointment->doctor->user->name;
        $patientName = $this->appointment->patient->user->name;
        $status = $this->appointment->status->value;

        $message = $isPatient
            ? sprintf('Appointment with Dr. %s is now %s', $doctorName, $status)
            : sprintf('Appointment with %s is now %s', $patientName, $status);

        return [
            'appointment_id' => $this->appointment->id,
            'patient_name' => $patientName,
            'doctor_name' => $doctorName,
            'appointment_date' => $this->appointment->appointment_date->toDateTimeString(),
            'status' => $status,
            'message' => $message,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
