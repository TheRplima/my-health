<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ManageNotificationsDispatcher;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\TelegramBotCallback;
use App\Jobs\WaterIntakeReminder;
use App\Jobs\GetTelegramUpdates;
use App\Jobs\ChatBot;
use App\Jobs\ReActiveSnoozedNotifications;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('telegram:poll-updates')->withoutOverlapping()->everySecond();
        $schedule->job(new TelegramBotCallback())->everyTwoSeconds()->withoutOverlapping()->name('telegram-bot-callback');
        $schedule->job(new WaterIntakeReminder())->everyMinute()->withoutOverlapping()->name('water-intake-reminder');
        $schedule->job(new ReActiveSnoozedNotifications())->everyMinute()->name('re-active-snoozed-notifications');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
