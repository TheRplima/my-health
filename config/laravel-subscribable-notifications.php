<?php

use App\Notifications\WaterIntakeReminderDatabase;
use App\Notifications\WaterIntakeReminderTelegram;
use App\Notifications\WaterIntakeReminderMail;

return [
    'user_model' => App\Models\User::class,

    'subscribable_notifications' => [
        WaterIntakeReminderDatabase::class,
        WaterIntakeReminderTelegram::class,
        WaterIntakeReminderMail::class,
    ],
];
