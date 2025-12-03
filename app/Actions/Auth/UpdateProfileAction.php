<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateProfileAction
{
    public function execute(User $user, array $data): User
    {
        DB::transaction(function () use ($user, $data) {
            $userData = array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if (isset($data['password'])) {
                $userData['password'] = bcrypt($data['password']);
            }

            $user->update($userData);

            if ($user->patient) {
                $user->patient->update(array_filter([
                    'phone' => $data['phone'] ?? null,
                    'date_of_birth' => $data['date_of_birth'] ?? null,
                ]));
            }
        });

        return $user->fresh()->load('patient');
    }
}
