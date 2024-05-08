<?php

namespace App\Jobs;

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

class TelegramBotCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $allowedModels = ['WaterIntake', 'WeightControl'];

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
        $updates = TelegramUpdates::create()
            ->latest()
            ->options([
                'timeout' => 0,
                'allowed_updates' => "callback_query"
            ])
            ->get();

        if ($updates['ok'] && count($updates['result']) > 0) {
            foreach ($updates['result'] as $update) {
                $updateId = $update['update_id'];
                $callback = $update['callback_query'];
                $latestUpdateIds = Cache::get('lastest_update_ids');

                if ($callback && (($latestUpdateIds !== null && !in_array($updateId,  $latestUpdateIds)) || $latestUpdateIds === null)) {
                    $latestUpdateIds[] = $updateId;
                    Cache::put('lastest_update_ids', $latestUpdateIds, 60 * 24 * 7);

                    $chatId = $callback['message']['chat']['id'];
                    $data = $callback['data'];
                    $model = explode('_', $data)[0];
                    $function = explode('_', $data)[1];
                    $field = explode(':', explode('_', $data)[2])[0];
                    $value = explode(':', explode('_', $data)[2])[1];

                    $user = User::where('telegram_user_id', $chatId)->first();
                    if ($user) {
                        if (in_array($model, $this->allowedModels)) {
                            $model = '\\App\\Models\\' . ucfirst($model);

                            $payload = [
                                'user_id' => $user->id,
                                $field => $value
                            ];

                            $model::$function($payload);

                            //return back a message to user telegram chat saying that the operation was successful
                            $message = TelegramMessage::create()
                                ->to($user->telegram_user_id)
                                ->content('Registro realizado com sucesso!');

                            $message->send();
                            Log::info('User with ID: ' . $user->id . ' has updated ' . $model . ' with ' . $field . ' = ' . $value . ' received from Telegram Bot Callback update id: ' . $updateId);
                        }
                    }
                }
            }
        }
    }
}
