<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Appointment::class);
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|integer|exists:doctors,id',
            'appointment_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'payment_status' => ['nullable', Rule::enum(PaymentStatus::class)],
            'payment_intent_id' => 'nullable|string',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->hasConflictingAppointment()) {
                $validator->errors()->add('appointment_date', 'This time slot is already booked');
            }
        });
    }

    private function hasConflictingAppointment(): bool
    {
        return Appointment::where('doctor_id', $this->doctor_id)
            ->where('appointment_date', $this->appointment_date)
            ->whereIn('status', [AppointmentStatus::PENDING->value, AppointmentStatus::CONFIRMED->value])
            ->exists();
    }
}
