<?php

namespace App\Listeners;

use App\Events\WeightControlReminderEvent;
use App\Notifications\WeightControlReminderDatabase;
use App\Notifications\WeightControlReminderMail;
use App\Notifications\WeightControlReminderTelegram;

class NotifyWeightControlReminder
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
    public function handle(WeightControlReminderEvent $event): void
    {
        WeightControlReminderDatabase::dispatch($event->user);
        WeightControlReminderMail::dispatch($event->user);
        WeightControlReminderTelegram::dispatch($event->user);
    }
}
