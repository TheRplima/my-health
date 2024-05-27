<?php

use App\Notifications\WaterIntakeReminderDatabase;
use App\Notifications\WaterIntakeReminderTelegram;
use App\Notifications\WaterIntakeReminderMail;
use App\Notifications\WeightControlReminderDatabase;
use App\Notifications\WeightControlReminderMail;
use App\Notifications\WeightControlReminderTelegram;

return [
    'user_model' => App\Models\User::class,

    'subscribable_notifications' => [
        WaterIntakeReminderDatabase::class,
        WaterIntakeReminderTelegram::class,
        WaterIntakeReminderMail::class,
        WeightControlReminderDatabase::class,
        WeightControlReminderTelegram::class,
        WeightControlReminderMail::class,
    ],
];
