<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Dr. John Smith',
                'specialization' => 'Cardiology',
                'email' => 'john.smith@hospital.com',
                'phone' => '+1234567890',
            ],
            [
                'name' => 'Dr. Sarah Johnson',
                'specialization' => 'Dermatology',
                'email' => 'sarah.johnson@hospital.com',
                'phone' => '+1234567891',
            ],
            [
                'name' => 'Dr. Michael Brown',
                'specialization' => 'Orthopedics',
                'email' => 'michael.brown@hospital.com',
                'phone' => '+1234567892',
            ],
            [
                'name' => 'Dr. Emily Davis',
                'specialization' => 'Pediatrics',
                'email' => 'emily.davis@hospital.com',
                'phone' => '+1234567893',
            ],
            [
                'name' => 'Dr. James Wilson',
                'specialization' => 'Neurology',
                'email' => 'james.wilson@hospital.com',
                'phone' => '+1234567894',
            ],
            [
                'name' => 'Dr. Lisa Martinez',
                'specialization' => 'Cardiology',
                'email' => 'lisa.martinez@hospital.com',
                'phone' => '+1234567895',
            ],
            [
                'name' => 'Dr. Robert Taylor',
                'specialization' => 'Dermatology',
                'email' => 'robert.taylor@hospital.com',
                'phone' => '+1234567896',
            ],
            [
                'name' => 'Dr. Jennifer Anderson',
                'specialization' => 'Orthopedics',
                'email' => 'jennifer.anderson@hospital.com',
                'phone' => '+1234567897',
            ],
            [
                'name' => 'Dr. David Thomas',
                'specialization' => 'Pediatrics',
                'email' => 'david.thomas@hospital.com',
                'phone' => '+1234567898',
            ],
            [
                'name' => 'Dr. Maria Garcia',
                'specialization' => 'Neurology',
                'email' => 'maria.garcia@hospital.com',
                'phone' => '+1234567899',
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}
