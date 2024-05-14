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
        $enabled = config('water-intake-reminder.enabled');
        $interval = config('water-intake-reminder.interval');
        $snooze = config('water-intake-reminder.snooze');
        $start = config('water-intake-reminder.start');
        $end = config('water-intake-reminder.end');
        $subscribable_notifications = config('water-intake-reminder.subscribable_notifications');

        if (!$enabled) {
            return;
        }

        $users = User::all();
        foreach ($users as $user) {
            if ($user->id != 1) {
                continue;
            }
            //loop at subscribable channels to check if user has any subscription, if not skip
            $hasSubscribable = false;
            foreach ($subscribable_notifications as $subscribable) {
                if ($user->notificationSubscriptions()->forType($subscribable)->exists()) {
                    $hasSubscribable = true;
                    break;
                }
            }
            if (!$hasSubscribable) {
                continue;
            }

            // If the user has disabled notifications, skip
            $disableNotification = Cache::get('disable_notification_' . $user->id);
            if ($disableNotification === 0 || ($disableNotification && now()->diffInMinutes($disableNotification) < 0)) {
                continue;
            }

            //if now is before 08:00 and after 23:00, skip
            if (!now()->between($start, $end)) {
                continue;
            }

            // If the user has not set a daily water amount, skip
            $goal = $user->daily_water_amount;
            if ($goal === null) {
                continue;
            }

            // Get the amount ingested and the goal of the user. If the amount ingested is more than the goal, skip
            $waterIntakesToday = $user->waterIntakeToday();
            $amountIngested = $waterIntakesToday->sum('amount');
            if ($amountIngested >= $goal) {
                continue;
            }

            //if last registered drink is less than 1 hour ago, skip
            $lastDrink = $waterIntakesToday->latest()->first();
            if ($lastDrink && now()->diffInMinutes($lastDrink->created_at) < $interval) {
                continue;
            }

            //if last notification sent to user is less than 15 minutes ago, skip
            $lastNotification = Cache::get('lastest_notification_' . $user->id);
            if ($lastNotification && now()->diffInMinutes($lastNotification) < $snooze) {
                continue;
            }

            //send reminder
            WaterIntakeReminderEvent::dispatch($user);

            // Cache the last notification
            Cache::put('lastest_notification_' . $user->id, now());
        }
    }
}
