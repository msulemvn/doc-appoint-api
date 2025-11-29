<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ViewAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view', $this->route('appointment'));
    }

    public function rules(): array
    {
        return [];
    }
}
