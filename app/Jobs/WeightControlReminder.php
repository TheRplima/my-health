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
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class WeightControlReminder implements ShouldQueue
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
        $enabled = config('weight-control-reminder.enabled');
        $defaultNotificationSetting = (object)config('weight-control-reminder.default_notification_setting');
        $notificationTypes = config('weight-control-reminder.notification_types');

        if (!$enabled) {
            return;
        }

        $users = User::all();
        foreach ($users as $user) {
            $userNotificationSubscriptions = $user->getNotificationSubscriptions($notificationTypes);
            $subscribeManagement = new NotificationSubscriptionManager();
            foreach ($userNotificationSubscriptions as $notificationSubscription) {
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

                $lastWeight = $user->weightControl()->latest()->first();
                if ($lastWeight && now()->diffInMinutes($lastWeight->created_at) < $notificationSetting->interval) {
                    continue;
                }

                $user->notify(new $channel($user));
                $user->snoozeNotification($type, $notificationSetting->snooze);
                Log::info('Reminder sent to user ' . $user->id . ' via ' . $notificationSubscription->type, ['last_weight' => $lastWeight ? $lastWeight->created_at : null]);
            }
        }
    }
}
