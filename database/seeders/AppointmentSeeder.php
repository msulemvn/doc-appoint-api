<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $appointments = [
            [
                'patient_id' => 1,
                'doctor_id' => 1,
                'appointment_date' => Carbon::now()->addDays(1)->setTime(10, 0),
                'status' => AppointmentStatus::PENDING,
                'notes' => 'Regular checkup',
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 2,
                'appointment_date' => Carbon::now()->addDays(2)->setTime(14, 0),
                'status' => AppointmentStatus::CONFIRMED,
                'notes' => 'Skin consultation',
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 3,
                'appointment_date' => Carbon::now()->addDays(1)->setTime(11, 30),
                'status' => AppointmentStatus::PENDING,
                'notes' => 'Knee pain assessment',
            ],
            [
                'patient_id' => 4,
                'doctor_id' => 4,
                'appointment_date' => Carbon::now()->addDays(3)->setTime(9, 0),
                'status' => AppointmentStatus::CONFIRMED,
                'notes' => 'Child vaccination',
            ],
            [
                'patient_id' => 5,
                'doctor_id' => 5,
                'appointment_date' => Carbon::now()->addDays(2)->setTime(15, 30),
                'status' => AppointmentStatus::PENDING,
                'notes' => 'Headache consultation',
            ],
            [
                'patient_id' => 6,
                'doctor_id' => 1,
                'appointment_date' => Carbon::now()->subDays(5)->setTime(10, 0),
                'status' => AppointmentStatus::COMPLETED,
                'notes' => 'Heart checkup',
            ],
            [
                'patient_id' => 7,
                'doctor_id' => 2,
                'appointment_date' => Carbon::now()->subDays(2)->setTime(13, 0),
                'status' => AppointmentStatus::CANCELLED,
                'notes' => 'Acne treatment',
            ],
            [
                'patient_id' => 8,
                'doctor_id' => 3,
                'appointment_date' => Carbon::now()->addDays(4)->setTime(16, 0),
                'status' => AppointmentStatus::CONFIRMED,
                'notes' => 'Back pain follow-up',
            ],
            [
                'patient_id' => 9,
                'doctor_id' => 4,
                'appointment_date' => Carbon::now()->addDays(1)->setTime(14, 30),
                'status' => AppointmentStatus::PENDING,
                'notes' => 'Annual child checkup',
            ],
            [
                'patient_id' => 10,
                'doctor_id' => 5,
                'appointment_date' => Carbon::now()->addDays(5)->setTime(11, 0),
                'status' => AppointmentStatus::CONFIRMED,
                'notes' => 'Migraine consultation',
            ],
            [
                'patient_id' => 1,
                'doctor_id' => 6,
                'appointment_date' => Carbon::now()->addDays(3)->setTime(13, 30),
                'status' => AppointmentStatus::PENDING,
                'notes' => 'Cardiac stress test',
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 7,
                'appointment_date' => Carbon::now()->addDays(6)->setTime(10, 30),
                'status' => AppointmentStatus::CONFIRMED,
                'notes' => 'Eczema treatment follow-up',
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 8,
                'appointment_date' => Carbon::now()->subDays(1)->setTime(15, 0),
                'status' => AppointmentStatus::COMPLETED,
                'notes' => 'Post-surgery checkup',
            ],
            [
                'patient_id' => 4,
                'doctor_id' => 9,
                'appointment_date' => Carbon::now()->addDays(7)->setTime(9, 30),
                'status' => AppointmentStatus::CONFIRMED,
                'notes' => 'Well-child visit',
            ],
            [
                'patient_id' => 5,
                'doctor_id' => 10,
                'appointment_date' => Carbon::now()->addDays(2)->setTime(12, 0),
                'status' => AppointmentStatus::PENDING,
                'notes' => 'Neurological assessment',
            ],
        ];

        foreach ($appointments as $appointment) {
            Appointment::create($appointment);
        }
    }
}
