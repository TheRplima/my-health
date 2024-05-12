<?php

namespace App\Console;

use App\Http\Controllers\WaterIntakeController;
use App\Jobs\ChatBot;
use App\Jobs\GetTelegramUpdates;
use App\Jobs\ManageNotificationsDispatcher;
use App\Jobs\SubscribeToTelegramNotifications;
use App\Jobs\TelegramBotCallback;
use App\Jobs\WaterIntakeReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new GetTelegramUpdates())->everyFiveSeconds()->name('get-telegram-updates');
        $schedule->job(new TelegramBotCallback())->everyTenSeconds()->name('telegram-bot-callback');
        $schedule->job(new ManageNotificationsDispatcher())->everyTenSeconds()->name('manage-notifications-dispatcher');
        $schedule->job(new SubscribeToTelegramNotifications())->everyMinute()->name('subscribe-telegram-notifications');
        $schedule->job(new WaterIntakeReminder())->everyMinute()->name('water-intake-reminder');
        $schedule->job(new ChatBot())->everyTenSeconds()->name('chat-bot');
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
