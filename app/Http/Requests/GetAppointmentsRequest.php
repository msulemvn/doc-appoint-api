<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAppointmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Appointment::class);
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
        ];
    }
}
