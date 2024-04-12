<?php

namespace Database\Seeders;

use App\Models\WeightControl;
use Illuminate\Database\Seeder;

class WeightControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WeightControl::factory(200)->create();
    }
}
