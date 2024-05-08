<?php

namespace App\Console;

use App\Http\Controllers\WaterIntakeController;
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
        $schedule->job(new SubscribeToTelegramNotifications())->everyMinute()->name('subscribe-telegram-notifications');
        $schedule->job(new WaterIntakeReminder())->everyMinute()->name('water-intake-reminder');
        $schedule->job(new TelegramBotCallback(app(WaterIntakeController::class)))->everyFiveSeconds()->name('telegram-bot-callback');
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
