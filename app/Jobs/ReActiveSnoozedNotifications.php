<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;

class ReActiveSnoozedNotifications implements ShouldQueue
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
        //get all notification settings that are snoozed and re-activate them
        $snoozedNotifications = NotificationSetting::where('disabled', true)->whereNotNull('disabled_until')->get();
        foreach ($snoozedNotifications as $notificationSetting) {
            if ($notificationSetting->disabled_until->isPast()) {
                $notificationSetting->enable();
            }
        }
    }
}
