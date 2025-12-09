<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isPatient() || $user->isDoctor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isPatient()) {
            return $appointment->patient_id === $user->patient?->id;
        }

        if ($user->isDoctor()) {
            return $appointment->doctor_id === $user->doctor?->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isPatient();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isDoctor()) {
            return $appointment->doctor_id === $user->doctor?->id;
        }

        if ($user->isPatient()) {
            return $appointment->patient_id === $user->patient?->id;
        }

        return false;
    }
}
