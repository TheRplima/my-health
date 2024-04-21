<?php

namespace Database\Seeders;

use App\Models\WaterIngestion;
use Illuminate\Database\Seeder;

class WaterIngestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WaterIngestion::factory(200)->create();
    }
}
