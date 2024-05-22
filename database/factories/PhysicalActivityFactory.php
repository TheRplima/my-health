<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PhysicalActivity>
 */
class PhysicalActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'sport_id' => \App\Models\PhysicalActivitySport::factory(),
            'calories_burned' => $this->faker->randomFloat(2, 0, 1000),
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'duration' => $this->faker->randomFloat(2, 0, 1000),
            'effort_level' => $this->faker->randomFloat(2, 0, 10),
            'distance' => $this->faker->randomFloat(2, 0, 1000),
            'speed' => $this->faker->randomFloat(2, 0, 100),
            'steps' => $this->faker->randomNumber(),
            'pace' => $this->faker->randomFloat(2, 0, 100),
            'observations' => $this->faker->sentence(),
        ];
    }
}
