<?php

namespace Database\Seeders;

use App\Models\WaterIntake;
use Illuminate\Database\Seeder;

class WaterIntakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WaterIntake::factory(200)->create();
    }
}
