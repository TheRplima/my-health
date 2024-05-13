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
use Illuminate\Support\Facades\Log;

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
            // If the user has not set a daily water amount, skip
            if ($user->daily_water_amount === null) {
                continue;
            }

            // If the user has disabled notifications, skip
            $disableNotification = Cache::get('disable_notification_' . $user->id);
            if ($disableNotification === 0 || ($disableNotification && now()->diffInMinutes($disableNotification) < 0)) {
                continue;
            }

            //if last notification sent to user is less than 15 minutes ago, skip
            $lastNotification = Cache::get('lastest_notification_' . $user->id);
            if ($lastNotification && now()->diffInMinutes($lastNotification) < 15) {
                continue;
            }

            //if last registered drink is less than 1 hour ago, skip
            $waterIntakesToday = $user->waterIntakeToday();
            $lastDrink = $waterIntakesToday->latest()->first();
            if ($lastDrink && $lastDrink->created_at->diffInMinutes(now()) < 60) {
                continue;
            }

            // Get the amount ingested and the goal of the user. If the amount ingested is more than the goal, skip
            $amountIngested = $waterIntakesToday->sum('amount');
            $goal = $user->daily_water_amount;
            if ($amountIngested >= $goal) {
                continue;
            }

            //if now is before 08:00 and after 23:00, skip
            if (!now()->between('08:00', '23:00')) {
                continue;
            }

            //send reminder
            WaterIntakeReminderEvent::dispatch($user);
            Log::info('Water intake reminder sent to user ' . $user->id);

            // Cache the last notification
            Cache::put('lastest_notification_' . $user->id, now());
        }
    }
}
