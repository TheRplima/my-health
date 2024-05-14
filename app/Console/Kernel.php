<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ManageNotificationsDispatcher;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\TelegramBotCallback;
use App\Jobs\WaterIntakeReminder;
use App\Jobs\GetTelegramUpdates;
use App\Jobs\ChatBot;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->job(new GetTelegramUpdates())->everySecond()->withoutOverlapping()->name('get-telegram-updates');
        // $schedule->job(new TelegramBotCallback())->everyTwoSeconds()->withoutOverlapping()->name('telegram-bot-callback');
        // $schedule->job(new ManageNotificationsDispatcher())->everyTwoSeconds()->withoutOverlapping()->name('manage-notifications-dispatcher');
        $schedule->job(new WaterIntakeReminder())->everyMinute()->name('water-intake-reminder');
        // $schedule->job(new ChatBot())->everyTwoSeconds()->withoutOverlapping()->name('chat-bot');
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
