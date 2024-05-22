<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        // $this->call(WaterIntakeSeeder::class);
        // $this->call(WeightControlSeeder::class);
        // $this->call(WaterIntakeContainerSeeder::class);
        // $this->call(PhysicalActivityCategorySeeder::class);
        // $this->call(PhysicalActivitySportSeeder::class);
        // $this->call(PhysicalActivitySeeder::class);
    }
}
