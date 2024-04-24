<?php

namespace Database\Seeders;

use App\Models\WaterIntakeContainers;
use Illuminate\Database\Seeder;

class WaterIntakeContainersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WaterIntakeContainers::factory(5)->create();
    }
}
