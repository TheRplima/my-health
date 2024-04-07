<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->create([
            'name' => 'Rodrigo Lima',
            'email' => 'rplima.dev@gmail.com',
            'password' => Hash::make('102030405060708090'),
            'phone' => '35998094996',
            'gender' => 'M',
            'dob' => '1979-05-25',
            'active' => 1
        ]);
        // User::factory(50)->create();
    }
}
