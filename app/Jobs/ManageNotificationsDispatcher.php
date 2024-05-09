<?php

namespace App\Jobs;

use App\Http\Resources\TelegramUpdateCollection;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramUpdates;

class ManageNotificationsDispatcher implements ShouldQueue
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
        $storageUpdates = Cache::get('telegram_updates') ?? new TelegramUpdateCollection([]);
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

                $user = User::where('telegram_user_id', $chatId)->first();
                if ($user) {
                    if ($command === 'disableNotification') {
                        $disableNotification = Cache::get('disable_notification_' . $user->id);
                        if ($disableNotification === 0 || ($disableNotification && now()->diffInMinutes($disableNotification) < 0)) {
                            $msg = 'Notificações já estão desabilitadas';
                        } else {
                            if ($value > 0) {
                                $value = now()->addMinutes($value);
                                $msg = 'Notificações desabilitadas até às ' . $value->toTimeString() . ' minutos';
                                $log = 'Usuário ' . $user->id . ' desabilitou notificações até ' . $value->toTimeString();
                            } else {
                                $msg = 'Notificações desabilitadas até que você as habilite novamente';
                                $log = 'Usuário ' . $user->id . ' desabilitou notificações indefinidamente';
                            }
                            Cache::put('disable_notification_' . $user->id, $value);
                            Log::info($log);
                        }
                        $reply = TelegramMessage::create()
                            ->to($chatId)
                            ->content($msg);
                        $reply->send();
                    }
                    if ($command === 'enableNotification') {
                        $disableNotification = Cache::get('disable_notification_' . $user->id);
                        if ($disableNotification === null) {
                            $msg = 'Notificações já estão habilitadas';
                        } else {
                            $msg = 'Notificações habilitadas, a partir de agora você receberá notificações novamente';
                            Cache::forget('disable_notification_' . $user->id);
                            $disableNotification = Cache::get('disable_notification_' . $user->id);
                            Log::info('Usuário ' . $user->id . ' habilitou notificações novamente');
                        }
                        $reply = TelegramMessage::create()
                            ->to($chatId)
                            ->content($msg);
                        $reply->send();
                    }
                }
            }
        }
    }
}
