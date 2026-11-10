<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'location' => fake()->city(),
            'date' => now()->addDays(rand(5, 30)),
            'total_tickets' => 100,
            'ticket_price' => 20.00,
        ];
    }
}