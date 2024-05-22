<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhysicalActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //based on categories from PhysicalActivityCategorySeeder and sports from PhysicalActivitySportSeeder, i want to seed 500 physical activities, in the period of 1 year, with random dates, times, effort levels and observations, only for user id 1
        $physicalActivities = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 500; $i++) {
            $date = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s');
            $startTime = $faker->time('H:i');
            $duration = $faker->randomFloat(2, 1, 3);
            $endTime = date('H:i', strtotime($startTime) + $duration * 3600);

            $physicalActivities[] = [
                'user_id' => 1,
                'name' => $faker->sentence(3),
                'description' => $faker->text(),
                'sport_id' => $faker->numberBetween(1, 30),
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration' => $duration,
                'effort_level' => $faker->randomElement(['low', 'medium', 'high']),
                'calories_burned' => $faker->randomFloat(2, 50, 500),
                'observations' => $faker->text(),
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        \App\Models\PhysicalActivity::insert($physicalActivities);
    }
}
