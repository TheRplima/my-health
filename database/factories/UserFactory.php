<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $arrayValues = ['M', 'F'];
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber,
            'gender' => $arrayValues[rand(0,1)],
            'dob' => fake()->date,
            'height' => fake()->numberBetween(60, 220),
            'weight' => fake()->numberBetween(50, 100),
            'daily_water_amount' => fake()->numberBetween(2000, 5000),
            'activity_level' => fake()->randomElement([0.2, 0.375, 0.55, 0.725, 0.9]),
            'active' => fake()->boolean
        ];
    }

}
