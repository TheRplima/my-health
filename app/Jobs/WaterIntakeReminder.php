<?php

namespace App\Jobs;

use App\Events\WaterIntakeReminderEvent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WaterIntakeReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            // Get the last drink of the user
            $lastDrink = $user->waterIntakeToday()->latest()->first();
            //get the last notification of type App\Notifications\WaterIntakeReminder of the user
            $lastNotification = $user->notifications()->where('type', 'App\Notifications\WaterIntakeReminder')->latest()->first();
            // If the user has not drunk water in the last hour and has not received a reminder in the 15 minutes, send a reminder and time range between 08:00 and 23:00
            if (now()->between(now()->setHour(8)->setMinute(0), now()->setHour(23)->setMinute(0)) && $lastDrink && $lastDrink->created_at->diffInMinutes() >= 60 && $lastNotification && $lastNotification->created_at->diffInMinutes() >= 15) {
                event(new WaterIntakeReminderEvent($user));
            }
        }
    }
}
