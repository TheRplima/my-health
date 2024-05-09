<?php

namespace App\Jobs;

use App\Events\WaterIntakeReminderEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
            $disableNotification = Cache::get('disable_notification_' . $user->id);
            // If the user has disabled notifications, skip
            if ($disableNotification === 0 || ($disableNotification && now()->diffInMinutes($disableNotification) < 0)) {
                continue;
            }
            // Get the last drink of the user
            $waterIntakesToday = $user->waterIntakeToday();
            $lastDrink = $waterIntakesToday->latest()->first();
            $amountIngested = $waterIntakesToday->sum('amount');
            $goal = $user->daily_water_amount;
            // Get the last notification of the user
            $lastNotification = Cache::get('lastest_otification');
            // If the user has not drunk water in the last hour and lastNotification cached was after 15 minutes and time range between 08:00 and 23:00  , send a reminder
            if (($amountIngested < $goal) && ($lastDrink && $lastDrink->created_at->diffInMinutes(now()) > 60) && (!$lastNotification || Carbon::parse($lastNotification)->diffInMinutes() >= 15) && now()->between('08:00', '23:00')) {
                // Dispatch the event
                WaterIntakeReminderEvent::dispatch($user);
                // Cache the last notification
                Cache::put('lastest_otification', now());
            }
        }
    }
}
