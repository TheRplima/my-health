<?php

namespace Database\Seeders;

use App\Models\WaterIntakeContainer;
use Illuminate\Database\Seeder;

class WaterIntakeContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WaterIntakeContainer::factory(5)->create();
    }
}
