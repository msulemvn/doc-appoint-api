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
            'name' => $this->name,
            'specialization' => $this->specialization,
            'email' => $this->email,
            'phone' => $this->phone,
            'available_slots' => $this->when(
                $request->has('date'),
                fn() => $this->getAvailableSlots($request->date)
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
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
            '13:00', '14:00', '15:00', '16:00', '17:00'
        ];

        // Get booked appointments for this date
        $bookedSlots = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->whereIn('status', [
                AppointmentStatus::PENDING->value,
                AppointmentStatus::CONFIRMED->value
            ])
            ->get()
            ->map(function ($appointment) {
                return Carbon::parse($appointment->appointment_date)->format('H:i');
            })
            ->toArray();

        // Return available slots (those not booked)
        return array_values(array_diff($workingHours, $bookedSlots));
    }
}
