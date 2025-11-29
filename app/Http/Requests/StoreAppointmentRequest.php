<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

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
        ];
    }
}
