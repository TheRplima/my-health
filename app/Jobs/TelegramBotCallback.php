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

class TelegramBotCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $allowedServices = ['WaterIntake', 'WeightControl'];

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
        $storageUpdates = new TelegramUpdateCollection(cache()->get('telegram_updates') ?? []);
        if (count($storageUpdates->toArray(request())) > 0) {
            $updates = $storageUpdates->getItemsByType('callback_query');
            $updateProcessed = false;
            foreach ($updates->toArray(request()) as $update) {
                $updateId = $update['update_id'];
                $chatId = $update['chat_id'];
                $serviceName = $update['command']['service'];
                $function = $update['command']['function'];
                $field = $update['command']['field'];
                $value = $update['command']['value'];

                $user = User::where('telegram_user_id', $chatId)->first();
                if ($user) {
                    if (in_array(ucfirst($serviceName), $this->allowedServices)) {
                        $updateProcessed = true;

                        $service = '\\App\\Services\\' . ucfirst($serviceName) . 'Service';
                        $repository = '\\App\\Repositories\\' . ucfirst($serviceName) . 'Repository';

                        $payload = [
                            'user_id' => $user->id,
                            $field => $value
                        ];

                        $serviceInstance = new $service(new $repository);
                        $object = $serviceInstance->$function($payload);

                        if ($object) {
                            //return back a message to user telegram chat saying that the operation was successful
                            $message = TelegramMessage::create()
                                ->to($user->telegram_user_id)
                                ->content('Registro realizado com sucesso!');

                            $message->send();

                            Log::info('User with ID: ' . $user->id . ' has updated ' . $serviceName . ' with ' . $field . ' = ' . $value . ' received from Telegram Bot Callback update id: ' . $updateId);
                        } else {
                            Log::error('User with ID: ' . $user->id . ' has tried to update ' . $serviceName . ' with ' . $field . ' = ' . $value . ' received from Telegram Bot Callback update id: ' . $updateId);
                        }
                    }
                }
                if ($updateProcessed) {
                    $storageUpdates = $storageUpdates->removeItem($updateId);
                }
            }
            Cache::forget('telegram_updates');
            Cache::put('telegram_updates', $storageUpdates);
        }
    }
}
