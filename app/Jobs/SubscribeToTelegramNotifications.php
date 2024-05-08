<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramUpdates;

class SubscribeToTelegramNotifications implements ShouldQueue
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
        // Response is an array of updates.
        $updates = TelegramUpdates::create()
            ->latest()
            ->options([
                'timeout' => 0,
                'allowed_updates' => "message"
            ])
            ->get();

        if ($updates['ok']) {
            // Chat ID
            $message = $updates['result'][count($updates['result']) - 1]['message'] ?? null;
            if ($message) {
                // check if text contains /start command
                if (strpos($message['text'], '/start') !== false) {
                    // Extract the deeplink from the message
                    $telegramUserDeeplink = explode(' ', $message['text'])[1];
                    $chatId = $message['chat']['id'];
                    // Find the user with the deeplink
                    $user = User::where('telegram_user_deeplink', $telegramUserDeeplink)->first();
                    if ($user && $user->telegram_user_id === null) {
                        // Update the user with the chat id
                        $user->telegram_user_id = $chatId;
                        $user->save();
                        //log result in laravel.log
                        Log::info('User with ID: ' . $user->id . ' has been subscribed to Telegram Notifications with Chat ID: ' . $chatId);
                    }
                }
            }
        }
    }
}
