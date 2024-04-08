<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WaterIngestion>
 */
class WaterIngestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(1, 10),
            'amount' => fake()->numberBetween(100, 1000),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
