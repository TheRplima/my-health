<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WaterIngestion>
 */
class WeightControlFactory extends Factory
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
            'weight' => fake()->numberBetween(48, 130),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
