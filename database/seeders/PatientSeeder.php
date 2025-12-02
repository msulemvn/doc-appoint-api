<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\PatientDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'name' => 'Alice Williams',
                'email' => 'alice.williams@email.com',
                'phone' => '+1987654321',
                'date_of_birth' => '1990-05-15',
            ],
            [
                'name' => 'Bob Thompson',
                'email' => 'bob.thompson@email.com',
                'phone' => '+1987654322',
                'date_of_birth' => '1985-08-22',
            ],
            [
                'name' => 'Carol Martinez',
                'email' => 'carol.martinez@email.com',
                'phone' => '+1987654323',
                'date_of_birth' => '1992-03-10',
            ],
            [
                'name' => 'Daniel Lee',
                'email' => 'daniel.lee@email.com',
                'phone' => '+1987654324',
                'date_of_birth' => '1988-11-05',
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@email.com',
                'phone' => '+1987654325',
                'date_of_birth' => '1995-07-18',
            ],
            [
                'name' => 'Frank Harris',
                'email' => 'frank.harris@email.com',
                'phone' => '+1987654326',
                'date_of_birth' => '1983-12-30',
            ],
            [
                'name' => 'Grace Clark',
                'email' => 'grace.clark@email.com',
                'phone' => '+1987654327',
                'date_of_birth' => '1991-09-25',
            ],
            [
                'name' => 'Henry White',
                'email' => 'henry.white@email.com',
                'phone' => '+1987654328',
                'date_of_birth' => '1987-04-12',
            ],
            [
                'name' => 'Isabel Rodriguez',
                'email' => 'isabel.rodriguez@email.com',
                'phone' => '+1987654329',
                'date_of_birth' => '1993-06-08',
            ],
            [
                'name' => 'Jack Miller',
                'email' => 'jack.miller@email.com',
                'phone' => '+1987654330',
                'date_of_birth' => '1989-01-20',
            ],
        ];

        foreach ($patients as $patientData) {
            $user = User::create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'password' => 'password',
                'role' => UserRole::PATIENT,
            ]);

            PatientDetail::create([
                'user_id' => $user->id,
                'phone' => $patientData['phone'],
                'date_of_birth' => $patientData['date_of_birth'],
            ]);
        }
    }
}
