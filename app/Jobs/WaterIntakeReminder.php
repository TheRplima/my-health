<?php

namespace App\Jobs;

use Asantibanez\LaravelSubscribableNotifications\NotificationSubscriptionManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use App\Models\User;

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
        $defaultNotificationSetting = (object)config('water-intake-reminder.default_notification_setting');

        if (!$enabled) {
            return;
        }

        $users = User::all();
        foreach ($users as $user) {
            $subscribeManagement = new NotificationSubscriptionManager();
            foreach ($user->notificationSubscriptions as $notificationSubscription) {
                $type = $notificationSubscription->type;
                $channel = $subscribeManagement->subscribableNotificationClassFromType($type);
                $notificationSetting = $user->getNotificationSetting($type);
                if (!$notificationSetting) {
                    $notificationSetting = $defaultNotificationSetting;
                }

                if ($user->isNotificationDisabled($type) || $user->isNotificationSnoozed($type)) {
                    continue;
                }

                if (!now()->between($notificationSetting->start, $notificationSetting->end)) {
                    continue;
                }

                $goal = $user->daily_water_amount;
                if ($goal === null) {
                    continue;
                }

                $waterIntakesToday = $user->waterIntakeToday();
                $amountIngested = $waterIntakesToday->sum('amount');
                if ($amountIngested >= $goal) {
                    continue;
                }

                $lastDrink = $waterIntakesToday->latest()->first();
                if ($lastDrink && now()->diffInMinutes($lastDrink->created_at) < $notificationSetting->interval) {
                    continue;
                }

                $user->notify(new $channel($user));
                $user->snoozeNotification($type, $notificationSetting->snooze);
                Log::info('Reminder sent to user ' . $user->id . ' via ' . $notificationSubscription->type, ['last_drink' => $lastDrink ? $lastDrink->created_at : null, 'amount_ingested' => $amountIngested ?? 0, 'goal' => $goal]);
            }
        }
    }
}
