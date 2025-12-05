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
                    'id' => $this->patient->id,
                    'user' => new UserResource($this->patient->user),
                    'phone' => $this->patient->phone,
                    'date_of_birth' => $this->patient->date_of_birth?->format('Y-m-d'),
                ]
            ),
            'doctor' => $this->when(
                $isPatient && $this->relationLoaded('doctor'),
                fn () => [
                    'id' => $this->doctor->id,
                    'user' => new UserResource($this->doctor->user),
                    'specialization' => $this->doctor->specialization,
                    'phone' => $this->doctor->phone,
                ]
            ),
        ];
    }
}
