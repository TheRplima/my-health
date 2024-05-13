<?php

namespace App\Jobs;

use App\Http\Resources\TelegramUpdateCollection;
use App\Http\Resources\UserResource;
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
use Ramsey\Uuid\Uuid;

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
                } else {
                    if (strtolower($command) == 'start') {
                        $updateProcessed = true;
                        if (!$chatBotStarted->contains($chatId)) {
                            $chatBotStarted->push($chatId);
                        }
                        $menu->put($chatId, 0);
                        $reply = TelegramMessage::create()
                            ->to($chatId)
                            ->content('Seja bem vindo! Você ainda não está cadastrado em nosso sistema. Gostaria de se cadastrar agora?' . "\n" . '1. Sim' . "\n" . '2. Não');
                        $reply->send();
                    }
                    if ($chatBotStarted->contains($chatId)) {
                        if ($menu->has($chatId)) {
                            $level = $menu->get($chatId);
                            //opçao para cancelar o cadastro a qualquer momento
                            if (strtolower($command) == 'cancelar' || strtolower($command) == 'cancel' || strtolower($command) == 'parar' || strtolower($command) == 'stop' || strtolower($command) == 'sair' || strtolower($command) == 'exit') {
                                $updateProcessed = true;
                                $chatBotStarted->forget($chatId);
                                $menu->forget($chatId);
                                Cache::forget('register_' . $chatId);
                                $level = -1;
                                $reply = TelegramMessage::create()
                                    ->to($chatId)
                                    ->content('Cadastro cancelado. Obrigado! Até a próxima.');
                                $reply->send();
                            }
                            if ($level == 0) {
                                if (strtolower($command) == 1 || strtolower($command) == 'sim' || strtolower($command) == 'yes') {
                                    $updateProcessed = true;
                                    $menu->put($chatId, 1);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você escolheu se cadastrar. Qual o seu nome completo?');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, new UserResource(new User()));
                                } elseif (strtolower($command) == 2 || strtolower($command) == 'nao' || strtolower($command) == 'não' || strtolower($command) == 'no') {
                                    $updateProcessed = true;
                                    $chatBotStarted->forget($chatId);
                                    $menu->forget($chatId);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Obrigado! Até a próxima.');
                                    $reply->send();
                                } elseif (strtolower($command) != 'start') {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Opção inválida. Gostaria de se cadastrar agora?' . "\n" . '1. Sim' . "\n" . '2. Não');
                                    $reply->send();
                                }
                            }
                            if ($level == 1) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a name to use in register, check if command is a name and ask for a email
                                if ($userResource && $userResource->name === null && $this->validateFullName($command)) {
                                    $updateProcessed = true;
                                    $userResource->name = $command;
                                    $menu->put($chatId, 2);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual o seu e-mail?');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Nome inválido. Qual o seu nome completo?');
                                    $reply->send();
                                }
                            }
                            if ($level == 2) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a email to use in register, check if command is a email and ask for a password
                                if ($userResource && $userResource->name !== null && $userResource->email === null && $this->validateEmail($command)) {
                                    $updateProcessed = true;
                                    $userResource->email = $command;
                                    $menu->put($chatId, 3);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual a sua senha?' . "\n" . 'A senha deve conter no mínimo 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial. Exemplo: Abc123@!');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('E-mail inválido. Qual o seu e-mail?');
                                    $reply->send();
                                }
                            }
                            if ($level == 3) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a password to use in register, check if command is a password and ask for a phone
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password === null && $this->validatePassword($command)) {
                                    $updateProcessed = true;
                                    $userResource->password = $command;
                                    $menu->put($chatId, 4);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual o seu telefone? (XX) XXXXX-XXXX');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Senha inválida. Qual a sua senha?' . "\n" . 'A senha deve conter no mínimo 8 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial. Exemplo: Abc123@!');
                                    $reply->send();
                                }
                            }
                            if ($level == 4) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a phone to use in register, check if command is a phone and ask for a gender
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone === null && $this->validatePhoneNumber($command)) {
                                    $updateProcessed = true;
                                    $phone = preg_replace('/[^0-9]/', '', $command);
                                    $userResource->phone = $phone;
                                    $menu->put($chatId, 5);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual o seu gênero? (M/F)');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Telefone inválido. Qual o seu telefone? (XX) XXXXX-XXXX');
                                    $reply->send();
                                }
                            }
                            if ($level == 5) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a gender to use in register, check if command is a string, only char M or F and ask for birthdate
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone !== null && $userResource->gender == null && strtolower($command) == 'm' || strtolower($command) == 'f') {
                                    $updateProcessed = true;
                                    $gender = strtolower($command) == 'm' ? 'M' : 'F';
                                    $userResource->gender = $gender;
                                    $menu->put($chatId, 6);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual a sua data de nascimento? (dd/mm/aaaa)');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Gênero inválido. Qual o seu gênero? (M/F)');
                                    $reply->send();
                                }
                            }
                            if ($level == 6) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a birthdate to use in register, check if command is a date and ask for a height
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone !== null && $userResource->gender !== null && $userResource->dob === null && $this->validateBirthdayDate($command)) {
                                    $updateProcessed = true;
                                    $dob = substr($command, 6, 4) . '-' . substr($command, 3, 2) . '-' . substr($command, 0, 2);
                                    $userResource->dob = $dob;
                                    $menu->put($chatId, 7);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual a sua altura? (em cm)');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Data de nascimento inválida. Qual a sua data de nascimento? (dd/mm/aaaa)');
                                    $reply->send();
                                }
                            }
                            if ($level == 7) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a height to use in register, check if command is a number and ask for a weight
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone !== null && $userResource->gender !== null && $userResource->dob !== null && $userResource->height === null && (is_numeric($command) && $command >= 145 && $command <= 220)) {
                                    $updateProcessed = true;
                                    $userResource->height = $command;
                                    $menu->put($chatId, 8);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual o seu peso? (em kg)');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Altura inválida. Qual a sua altura? (em cm)');
                                    $reply->send();
                                }
                            }
                            if ($level == 8) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a weight to use in register, check if command is a number and ask for a daily water amount
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone !== null && $userResource->gender !== null && $userResource->dob !== null && $userResource->height !== null && $userResource->weight === null && (is_numeric($command) && $command >= 50 && $command <= 140)) {
                                    $updateProcessed = true;
                                    $userResource->weight = $command;
                                    $menu->put($chatId, 9);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Qual a quantidade de água que você deseja consumir diariamente (em ml)?');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Peso inválido. Qual o seu peso? (em kg)');
                                    $reply->send();
                                }
                            }
                            if ($level == 9) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a daily water amount to use in register, check if command is a number, finish register and send a message asking if the user wand to receive notifications via telegram
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone !== null && $userResource->gender !== null && $userResource->dob !== null && $userResource->height !== null && $userResource->weight !== null && $userResource->daily_water_amount === null && (is_numeric($command) && $command >= 1000 && $command <= 10000)) {
                                    $updateProcessed = true;
                                    $userResource->daily_water_amount = $command;
                                    $menu->put($chatId, 10);
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Você deseja receber notificações via Telegram? (S/N)');
                                    $reply->send();
                                    Cache::put('register_' . $chatId, $userResource);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Quantidade de água inválida. Qual a quantidade de água que você deseja consumir diariamente (em ml)?');
                                    $reply->send();
                                }
                            }
                            if ($level == 10) {
                                $userResource = Cache::get('register_' . $chatId);
                                //user sent a notification option to use in register, check if command is a string, only char S or N, finish register and send a message with a success message
                                if ($userResource && $userResource->name !== null && $userResource->email !== null && $userResource->password !== null && $userResource->phone !== null && $userResource->gender !== null && $userResource->dob !== null && $userResource->height !== null && $userResource->weight !== null && $userResource->daily_water_amount !== null && (strtolower($command) == 's' || strtolower($command) == 'n')) {
                                    $updateProcessed = true;
                                    $notifications = strtolower($command) == 's' ? 1 : 0;
                                    if ($notifications) {
                                        $userResource->telegram_user_id = $chatId;
                                        $userResource->telegram_user_deeplink = Uuid::uuid4();
                                    }
                                    $user = User::create($userResource->toArray(request()));
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Cadastro realizado com sucesso! Você já pode utilizar o menu de opções.' . "\n" . $this->getMenu(0));
                                    $reply->send();
                                    Cache::forget('register_' . $chatId);
                                    $chatBotStarted->pull($chatId);
                                    $chatBotStarted->push($user->id);
                                    $menu->pull($chatId);
                                    $menu->put($user->id, 0);
                                    Cache::put('menu', $menu);
                                } else {
                                    $updateProcessed = true;
                                    $reply = TelegramMessage::create()
                                        ->to($chatId)
                                        ->content('Opção inválida. Você deseja receber notificações via Telegram? (S/N)');
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

    public function validateFullName($nome)
    {
        //Validar nome completo. Deve conter no mínimo 2 palavras, com pelo menos 2 caracteres em cada palavra, sem caracteres especiais, porém permitir acentos
        return preg_match('/^([a-zA-ZÀ-ú]+ ){1,}[a-zA-ZÀ-ú]+$/', $nome);
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function validatePassword($password)
    {
        // Minimum eight characters, at least one uppercase letter, one lowercase letter and one number. allow special char
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,}$/', $password);
    }

    public function validatePhoneNumber($phone)
    {
        // (99) 99999-9999 or (99) 9999-9999 or 99999-9999 or 9999-9999 or 99999999999 or 9999999999
        return preg_match('/^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/', $phone);
    }

    public function validateBirthdayDate($date)
    {
        // dd/mm/yyyy and more then 12 years old
        $date = explode('/', $date);
        if (count($date) == 3) {
            $day = $date[0];
            $month = $date[1];
            $year = $date[2];
            if (checkdate($month, $day, $year)) {
                $date = \Carbon\Carbon::createFromDate($year, $month, $day);
                return $date->diffInYears(now()) >= 12;
            }
        }
        return false;
    }
}
