<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'patient' => $this->when($this->relationLoaded('patient') && $this->patient, fn () => [
                'phone' => $this->patient->phone,
                'date_of_birth' => $this->patient->date_of_birth?->format('Y-m-d'),
            ]),
            'doctor' => $this->when($this->relationLoaded('doctor') && $this->doctor, fn () => [
                'specialization' => $this->doctor->specialization,
                'phone' => $this->doctor->phone,
            ]),
        ];
    }
}
