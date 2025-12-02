<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\PatientDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterPatientAction
{
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => UserRole::PATIENT,
            ]);

            PatientDetail::create([
                'user_id' => $user->id,
                'phone' => $data['phone'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
            ]);

            return $user->load('patient');
        });
    }
}
