<?php

namespace App\Listeners;

use App\Notifications\WaterIntakeReminderDatabase;
use App\Notifications\WaterIntakeReminderMail;
use App\Notifications\WaterIntakeReminderTelegram;

class NotifyWaterIntakeReminder
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        WaterIntakeReminderDatabase::dispatch($event->user);
        WaterIntakeReminderMail::dispatch($event->user);
        WaterIntakeReminderTelegram::dispatch($event->user);
    }
}
