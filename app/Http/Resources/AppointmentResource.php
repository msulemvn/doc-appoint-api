<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('api')->user();
        $isPatient = $user && $user->isPatient();
        $isDoctor = $user && $user->isDoctor();

        return [
            'id' => $this->id,
            'appointment_date' => $this->appointment_date,
            'status' => $this->status->label(),
            'notes' => $this->notes,
            'updated_at' => $this->when(
                $this->wasRecentlyCreated === false && $this->wasChanged('status'),
                $this->updated_at
            ),
            'patient' => $this->when(
                $isDoctor && $this->relationLoaded('patient'),
                fn () => [
                    'name' => $this->patient->user->name,
                    'email' => $this->patient->user->email,
                    'phone' => $this->patient->phone,
                    'date_of_birth' => $this->patient->date_of_birth?->format('Y-m-d'),
                ]
            ),
            'doctor' => $this->when(
                $isPatient && $this->relationLoaded('doctor'),
                fn () => [
                    'name' => $this->doctor->user->name,
                    'specialization' => $this->doctor->specialization,
                    'phone' => $this->doctor->phone,
                ]
            ),
        ];
    }
}
