<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => \App\Enums\UserRole::PATIENT,
        ]);

        \App\Models\PatientDetail::create([
            'user_id' => $testUser->id,
            'phone' => '+1234567890',
            'date_of_birth' => '1990-01-01',
        ]);

        $this->call([
            DoctorSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
