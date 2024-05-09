<?php

namespace App\Jobs;

use App\Models\User;
use Asantibanez\LaravelSubscribableNotifications\NotificationSubscriptionManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;
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
        $storageUpdates = Cache::get('telegram_updates') ?? [];
        if (count($storageUpdates->toArray(request())) > 0) {
            $updates = $storageUpdates->getItemsByType('message');
            foreach ($updates->toArray(request()) as $update) {
                $updateId = $update['update_id'];
                $storageUpdates = $storageUpdates->removeItem($updateId);
                Cache::forget('telegram_updates');
                Cache::put('telegram_updates', $storageUpdates);

                $chatId = $update['chat_id'];
                $command = $update['command']['service'];
                $value = $update['command']['value'];
                if ($command === 'start') {
                    // Find the user with the deeplink
                    $user = User::where('telegram_user_deeplink', $value)->first();
                    if ($user && $user->telegram_user_id === null) {
                        // Update the user with the chat id
                        $user->telegram_user_id = $chatId;
                        $user->save();

                        $subscribeManagement = new NotificationSubscriptionManager();
                        $subscribeManagement->subscribe($user, 'App\\Notifications\\WaterIntakeReminderDatabase');
                        $subscribeManagement->subscribe($user, 'App\\Notifications\\WaterIntakeReminderTelegram');

                        $reply = TelegramMessage::create()
                            ->to($chatId)
                            ->content('Você foi inscrito para Notificações no Telegram! Agora você receberá notificações importantes diretamente no seu Telegram!');
                        $reply->send();

                        Log::info('Usuário com ID: ' . $user->id . ' foi inscrito para Notificações no Telegram com Chat ID: ' . $chatId);
                    }
                }
            }
        }

        if ($updates['ok']) {
            // Chat ID
            $message = $updates['result'][count($updates['result']) - 1]['message'] ?? null;
            if ($message) {
                // check if text contains /start command
                if (strpos($message['text'], '/start ') !== false) {
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
