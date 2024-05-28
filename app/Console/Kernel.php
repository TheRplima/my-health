<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\ReActiveSnoozedNotifications;
use App\Jobs\WeightControlReminder;
use App\Jobs\WaterIntakeReminder;

class Kernel extends ConsoleKernel
{
    protected $enabled;

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $this->enabled = config('services.scheduler-routines.enabled');
        if (!$this->enabled) {
            return;
        }
        $schedule->command('telegram:poll-updates')->withoutOverlapping()->everySecond();
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
