<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Chat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        return [
            'user1_id' => $user1->id,
            'user2_id' => $user2->id,
            'appointment_id' => Appointment::factory()->create([
                'patient_id' => $user1->id,
                'doctor_id' => $user2->id,
            ])->id,
        ];
    }

    /**
     * Indicate that the chat has no appointment.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function noAppointment()
    {
        return $this->state(function (array $attributes) {
            return [
                'appointment_id' => null,
            ];
        });
    }
}
