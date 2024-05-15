<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::whereNotNull('telegram_user_id')->get();
        foreach ($users as $user) {
            \App\Models\NotificationSetting::factory()->create([
                'user_id' => $user->id,
                'type' => 'water-intake-reminder-database',
            ]);
            \App\Models\NotificationSetting::factory()->create([
                'user_id' => $user->id,
                'type' => 'water-intake-reminder-telegram',
            ]);
        }
    }
}
