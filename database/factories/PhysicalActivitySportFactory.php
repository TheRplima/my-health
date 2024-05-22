<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhysicalActivitySport>
 */
class PhysicalActivitySportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'category_id' => \App\Models\PhysicalActivityCategory::factory(),
            'calories_burned_per_minute' => $this->faker->randomFloat(2, 0, 1000),
            'metabolic_equivalent' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
