<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Test Patient',
            'email' => 'patient@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        User::factory()->create([
            'name' => 'Test Doctor',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        User::factory()->count(10)->create(); // Create 10 random users
    }
}
