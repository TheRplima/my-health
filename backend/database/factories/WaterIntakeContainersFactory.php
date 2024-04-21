<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WaterIntakeContainers>
 */
class WaterIntakeContainersFactory extends Factory
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
            'name' => fake()->word(),
            'size' => fake()->numberBetween(200, 2000),
            'icon' => fake()->word(),
            'active' => fake()->boolean(),
        ];
    }
}
