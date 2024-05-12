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

class ChatBot implements ShouldQueue
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
            $updateProcessed = false;
            $chatBotStarted = collect(Cache::get('ChatBotStarted') ?? []);
            $menu = Cache::get('menu') ?? collect([]);
            foreach ($updates->toArray(request()) as $update) {
                $updateId = $update['update_id'];
                $chatId = $update['chat_id'];
                $command = $update['command']['service'];
                $value = $update['command']['value'];

                $user = User::where('telegram_user_id', $chatId)->first();
                if ($user) {
                    if (strtolower($command) == 'menu' || strtolower($command) == 'showmenu' || strtolower($command) == 'startbot' || strtolower($command) == 'startmenu') {
                        $updateProcessed = true;
                        if (!$chatBotStarted->contains($user->id)) {
                            $chatBotStarted->push($user->id);
                        }
                        $menu->put($user->id, 0);
                        $reply = TelegramMessage::create()
                            ->to($chatId)
                            ->content($this->getMenu(0));
                        $reply->send();
                    }
                    if ($chatBotStarted->contains($user->id)) {
                        if ($menu->has($user->id)) {
                            $level = $menu->get($user->id);
                            if ($level == 0) {
                                if (strtolower($command) == 1 || strtolower($command) == 'agua' || strtolower($command) == 'water') {
                                    $updateProcessed = true;
                                    $menu->put($user->id, 1);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($this->getMenu(1));
                                    $reply->send();
                                }
                                if (strtolower($command) == 2 || strtolower($command) == 'peso' || strtolower($command) == 'weight') {
                                    $updateProcessed = true;
                                    $menu->put($user->id, 2);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($this->getMenu(2));
                                    $reply->send();
                                }
                                if (strtolower($command) == 3 || strtolower($command) == 'notificacoes' || strtolower($command) == 'notifications') {
                                    $updateProcessed = true;
                                    $menu->put($user->id, 3);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($this->getMenu(3));
                                    $reply->send();
                                }
                                if (strtolower($command) == 4 || strtolower($command) == 'sair' || strtolower($command) == 'exit') {
                                    $updateProcessed = true;
                                    $chatBotStarted->forget($user->id);
                                    $menu->forget($user->id);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você saiu do menu.');
                                    $reply->send();
                                }
                            }
                            if ($level == 1) {
                                if (strtolower($command) == 1 || strtolower($command) == 'registrar' || strtolower($command) == 'register') {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você escolheu registrar consumo de água. Qual a quantidade de água que você consumiu (em ml)?');
                                    $reply->send();
                                }
                                if (strtolower($command) == 2 || strtolower($command) == 'ver' || strtolower($command) == 'show') {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você escolheu ver consumo de água.' . "\n\n" . $this->showWaterIntakeToday($user) . "\n" . 'O que deseja fazer agora?' . "\n" . '1. Registrar consumo' . "\n" . '2. Ver consumo de hoje' . "\n" . '3. Voltar ao Menu');
                                    $reply->send();
                                }
                                if (strtolower($command) == 3 || strtolower($command) == 'voltar' || strtolower($command) == 'back') {
                                    $updateProcessed = true;
                                    $menu->put($user->id, 0);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($this->getMenu(0));
                                    $reply->send();
                                }
                                // Register water intake
                                if (is_numeric($command) && $command >= 10) {
                                    $updateProcessed = true;
                                    $service = '\\App\\Services\\WaterIntakeService';
                                    $repository = '\\App\\Repositories\\WaterIntakeRepository';

                                    $payload = [
                                        'user_id' => $user->id,
                                        'amount' => $command
                                    ];

                                    $serviceInstance = new $service(new $repository);
                                    $object = $serviceInstance->create($payload);

                                    if ($object) {
                                        //return back a message to user telegram chat saying that the operation was successful
                                        $message = TelegramMessage::create()
                                            ->to($user->telegram_user_id)
                                            ->content('Registro realizado com sucesso!' . "\n\n" . 'O que deseja fazer agora?' . "\n" . '1. Registrar consumo' . "\n" . '2. Ver consumo de hoje' . "\n" . '3. Voltar ao Menu');

                                        $message->send();

                                        Log::info('User with ID: ' . $user->id . ' has updated Water with amount = ' . $command . ' received from Telegram Bot Callback update id: ' . $updateId);
                                    } else {
                                        Log::error('User with ID: ' . $user->id . ' has tried to update Water with amount = ' . $command . ' received from Telegram Bot Callback update id: ' . $updateId);
                                    }
                                }
                            }
                            if ($level == 2) {
                                if (strtolower($command) == 1 || strtolower($command) == 'registrar' || strtolower($command) == 'register') {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você escolheu registrar peso. Qual o seu peso atual (em kg)?');
                                    $reply->send();
                                }
                                if (strtolower($command) == 2 || strtolower($command) == 'ver' || strtolower($command) == 'show') {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você escolheu ver peso.' . "\n\n" . $this->showWeightToday($user) . "\n" . 'O que deseja fazer agora?' . "\n" . '1. Registrar peso' . "\n" . '2. Ver peso' . "\n" . '3. Voltar ao Menu');
                                    $reply->send();
                                }
                                if (strtolower($command) == 3 || strtolower($command) == 'voltar' || strtolower($command) == 'back') {
                                    $updateProcessed = true;
                                    $menu->put($user->id, 0);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($this->getMenu(0));
                                    $reply->send();
                                }
                                // Register weight
                                if (is_numeric($command) && $command >= 10) {
                                    $updateProcessed = true;
                                    $service = '\\App\\Services\\WeightControlService';
                                    $repository = '\\App\\Repositories\\WeightControlRepository';

                                    $payload = [
                                        'user_id' => $user->id,
                                        'weight' => $command
                                    ];

                                    $serviceInstance = new $service(new $repository);
                                    $object = $serviceInstance->create($payload);

                                    if ($object) {
                                        //return back a message to user telegram chat saying that the operation was successful
                                        $message = TelegramMessage::create()
                                            ->to($user->telegram_user_id)
                                            ->content('Registro realizado com sucesso!' . "\n\n" . 'O que deseja fazer agora?' . "\n" . '1. Registrar peso' . "\n" . '2. Ver peso' . "\n" . '3.Voltar ao Menu');

                                        $message->send();

                                        Log::info('User with ID: ' . $user->id . ' has updated Weight with amount = ' . $command . ' received from Telegram Bot Callback update id: ' . $updateId);
                                    } else {
                                        Log::error('User with ID: ' . $user->id . ' has tried to update Weight with amount = ' . $command . ' received from Telegram Bot Callback update id: ' . $updateId);
                                    }
                                }
                            }
                            if ($level == 3) {
                                if (strtolower($command) == 1 || strtolower($command) == 'ativar' || strtolower($command) == 'activate') {
                                    $updateProcessed = true;
                                    $disableNotification = Cache::get('disable_notification_' . $user->id);
                                    if ($disableNotification === null) {
                                        $msg = 'Notificações já estão habilitadas';
                                    } else {
                                        $msg = 'Notificações habilitadas, a partir de agora você receberá notificações novamente';
                                        Cache::forget('disable_notification_' . $user->id);
                                        $disableNotification = Cache::get('disable_notification_' . $user->id);
                                    }
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($msg . "\n\n" . 'O que deseja fazer agora?' . "\n" . '1. Ativar notificações' . "\n" . '2. Desativar notificações' . "\n" . '3. Voltar ao Menu');
                                    $reply->send();
                                }
                                if (strtolower($command) == 2 || strtolower($command) == 'desativar' || strtolower($command) == 'deactivate') {
                                    $updateProcessed = true;
                                    $disableNotification = Cache::get('disable_notification_' . $user->id);
                                    if ($disableNotification === 0 || ($disableNotification && now()->diffInMinutes($disableNotification) < 0)) {
                                        $msg = 'Notificações já estão desabilitadas';
                                    } else {
                                        $msg = 'Notificações desabilitadas até que você as habilite novamente';
                                        Cache::put('disable_notification_' . $user->id, 0);
                                    }
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($msg . "\n\n" . 'O que deseja fazer agora?' . "\n" . '1. Ativar notificações' . "\n" . '2. Desativar notificações' . "\n" . '3. Voltar ao Menu');
                                    $reply->send();
                                }
                                if (strtolower($command) == 3 || strtolower($command) == 'voltar' || strtolower($command) == 'back') {
                                    $updateProcessed = true;
                                    $menu->put($user->id, 0);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content($this->getMenu(0));
                                    $reply->send();
                                }
                            }
                        }
                    }
                }
                if ($updateProcessed) {
                    $storageUpdates = $storageUpdates->removeItem($updateId);
                }
            }
            Cache::forget('telegram_updates');
            Cache::put('telegram_updates', $storageUpdates);
            Cache::put('ChatBotStarted', $chatBotStarted);
            Cache::put('menu', $menu);
            Log::info('ChatBotStarted: ' . json_encode($chatBotStarted->toArray()));
            Log::info('Menu: ' . json_encode($menu->toArray()));
        }
    }

    public function getMenu($level = 0): string
    {
        $menu = '';
        switch ($level) {
            case 0:
                $menu = 'Escolha uma das opções abaixo digitando o número.' . "\n" . '1. Água' . "\n" . '2. Peso' . "\n" . '3. Notificações' . "\n" . '4. Sair do Menu';
                break;
            case 1:
                $menu = 'Você entrou no módulo de controle de água. O que gostaria de fazer?' . "\n" . '1. Registrar consumo' . "\n" . '2. Ver consumo de hoje' . "\n" . '3. Voltar ao Menu';
                break;
            case 2:
                $menu = 'Você entrou no módulo de controle de peso. O que gostaria de fazer?' . "\n" . '1. Registrar peso' . "\n" . '2. Ver peso' . "\n" . '3. Voltar ao Menu';
                break;
            case 3:
                $menu = 'Você entrou no módulo de notificações. O que gostaria de fazer?' . "\n" . '1. Ativar notificações' . "\n" . '2. Desativar notificações' . "\n" . '3. Voltar ao Menu';
                break;
        }
        return $menu;
    }

    function showWaterIntakeToday($user, $asObject = false)
    {
        $service = '\\App\\Services\\WaterIntakeService';
        $repository = '\\App\\Repositories\\WaterIntakeRepository';

        $serviceInstance = new $service(new $repository);
        $object = $serviceInstance->getWaterIntakesByDay($user->id, now()->toDateString());

        if ($asObject) {
            return $object;
        }

        if ($object) {
            $total = 0;
            $list = '';
            foreach ($object as $item) {
                $total += $item->amount;
                $list .= $item->created_at->format('H:i') . ' - ';
                $list .= $item->amount . 'ml' . "\n";
            }

            $message = 'Sua meta de consumo diário é de *' . $user->daily_water_amount . 'ml*.' . "\n";
            $message .= 'Você já consumiu *' . $total . 'ml* de água hoje.' . "\n";

            if ($total >= $user->daily_water_amount) {
                $message .= 'Parabéns! Você já atingiu sua meta de consumo diário de água.' . "\n";
            } else {
                $message .= 'Faltam *' . ($user->daily_water_amount - $total) . 'ml* para atingir sua meta de consumo diário de água.' . "\n";
            }

            $message .= "\n" . 'Detalhes:' . "\n" . $list;

            return $message;
        }

        return 'Você ainda não consumiu água hoje.';
    }

    function showWeightToday($user, $asObject = false)
    {
        $service = '\\App\\Services\\WeightControlService';
        $repository = '\\App\\Repositories\\WeightControlRepository';

        $serviceInstance = new $service(new $repository);
        $object = $serviceInstance->getWeightControlsByMonth($user->id, now()->month);

        if ($asObject) {
            return $object;
        }

        if ($object) {
            $message = 'Seu peso atual é de *' . $user->weight . 'kg*.' . "\n\n";
            $message .= 'Detalhes:' . "\n";
            $message .= 'Peso registrado este mês:' . "\n";
            Log::info('Object: ' . json_encode($object->toArray()));
            foreach ($object as $item) {
                $message .= $item->created_at->format('d/m/Y') . ' - ';
                $message .= $item->weight . 'kg' . "\n";
            }

            return $message;
        }

        return 'Você não registrou seu peso este mês.';
    }
}
