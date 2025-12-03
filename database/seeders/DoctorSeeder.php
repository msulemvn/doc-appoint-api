<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\DoctorDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'John Smith',
                'specialization' => 'Cardiology',
                'email' => 'john.smith@hospital.com',
                'phone' => '+1234567890',
                'avatar' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'Sarah Johnson',
                'specialization' => 'Dermatology',
                'email' => 'sarah.johnson@hospital.com',
                'phone' => '+1234567891',
                'avatar' => null,
            ],
            [
                'name' => 'Michael Brown',
                'specialization' => 'Orthopedics',
                'email' => 'michael.brown@hospital.com',
                'phone' => '+1234567892',
                'avatar' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'Emily Davis',
                'specialization' => 'Pediatrics',
                'email' => 'emily.davis@hospital.com',
                'phone' => '+1234567893',
                'avatar' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'James Wilson',
                'specialization' => 'Neurology',
                'email' => 'james.wilson@hospital.com',
                'phone' => '+1234567894',
                'avatar' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'Lisa Martinez',
                'specialization' => 'Cardiology',
                'email' => 'lisa.martinez@hospital.com',
                'phone' => '+1234567895',
                'avatar' => null,
            ],
            [
                'name' => 'Robert Taylor',
                'specialization' => 'Dermatology',
                'email' => 'robert.taylor@hospital.com',
                'phone' => '+1234567896',
                'avatar' => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'Jennifer Anderson',
                'specialization' => 'Orthopedics',
                'email' => 'jennifer.anderson@hospital.com',
                'phone' => '+1234567897',
                'avatar' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'David Thomas',
                'specialization' => 'Pediatrics',
                'email' => 'david.thomas@hospital.com',
                'phone' => '+1234567898',
                'avatar' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400&h=400&fit=crop&crop=face',
            ],
            [
                'name' => 'Maria Garcia',
                'specialization' => 'Neurology',
                'email' => 'maria.garcia@hospital.com',
                'phone' => '+1234567899',
                'avatar' => null,
            ],
        ];

        foreach ($doctors as $doctorData) {
            $user = User::create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'password' => bcrypt('password'),
                'role' => UserRole::DOCTOR,
                'avatar' => $doctorData['avatar'],
            ]);

            DoctorDetail::create([
                'user_id' => $user->id,
                'specialization' => $doctorData['specialization'],
                'phone' => $doctorData['phone'],
            ]);
        }
    }
}
