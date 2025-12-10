<?php

namespace App\Http\Resources;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'name' => $this->user->doctor_name,
            'email' => $this->email ?? $this->user->email,
            'phone' => $this->phone,
            'specialization' => $this->specialization,
            'bio' => $this->bio,
            'years_of_experience' => $this->years_of_experience,
            'consultation_fee' => $this->consultation_fee,
            'license_number' => $this->license_number,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->doctor_name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar,
            ],
            'available_slots' => $this->when(
                $request->has('date'),
                fn () => $this->getAvailableSlots($request->date)
            ),
        ];
    }

    /**
     * Get available time slots for a specific date
     */
    private function getAvailableSlots(string $date): array
    {
        // Define working hours (9 AM to 5 PM with 1-hour slots)
        $workingHours = [
            '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00', '17:00',
        ];

        $bookedSlots = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', [
                AppointmentStatus::PENDING->value,
                AppointmentStatus::CONFIRMED->value,
            ])
            ->get()
            ->map(fn ($appointment) => Carbon::parse($appointment->appointment_date)->format('H:i'))
            ->toArray();

        return array_values(array_diff($workingHours, $bookedSlots));
    }
}
