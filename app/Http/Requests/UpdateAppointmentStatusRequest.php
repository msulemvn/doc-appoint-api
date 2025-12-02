<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('appointment'));
    }

    public function rules(): array
    {
        $user = $this->user();

        if ($user->isPatient()) {
            return [
                'status' => ['required', 'string', Rule::in(['cancelled'])],
            ];
        }

        if ($user->isDoctor()) {
            return [
                'status' => ['required', 'string', Rule::in(['confirmed', 'cancelled', 'completed'])],
            ];
        }

        return [
            'status' => ['required', 'string', Rule::in([])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $appointment = $this->route('appointment');
            $newStatus = AppointmentStatus::from($this->status);
            $currentStatus = $appointment->status;

            if (! $this->isValidTransition($currentStatus, $newStatus)) {
                $validator->errors()->add(
                    'status',
                    sprintf(
                        "Cannot change appointment status from '%s' to '%s'",
                        $currentStatus->label(),
                        $newStatus->label()
                    )
                );
            }
        });
    }

    private function isValidTransition(AppointmentStatus $current, AppointmentStatus $new): bool
    {
        $validTransitions = [
            AppointmentStatus::PENDING->value => [AppointmentStatus::CONFIRMED->value, AppointmentStatus::CANCELLED->value],
            AppointmentStatus::CONFIRMED->value => [AppointmentStatus::COMPLETED->value, AppointmentStatus::CANCELLED->value],
            AppointmentStatus::COMPLETED->value => [],
            AppointmentStatus::CANCELLED->value => [],
        ];

        return in_array($new->value, $validTransitions[$current->value] ?? []);
    }
}
