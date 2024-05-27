<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\WaterIntakeReminder;
use App\Jobs\ReActiveSnoozedNotifications;
use App\Jobs\WeightControlReminder;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('telegram:poll-updates')->withoutOverlapping()->everySecond();
        $schedule->job(new ReActiveSnoozedNotifications())->everyMinute()->name('re-active-snoozed-notifications');
        $schedule->job(new WaterIntakeReminder())->everyMinute()->withoutOverlapping()->name('water-intake-reminder');
        $schedule->job(new WeightControlReminder())->everyMinute()->withoutOverlapping()->name('weight-control-reminder');
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
