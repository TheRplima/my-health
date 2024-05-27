<?php

namespace App\Http\Controllers;

use App\Http\Resources\TelegramUpdateCollection;
use App\Http\Resources\TelegramUpdateResource;
use App\Models\User;
use App\Repositories\NotificationSettingRepository;
use App\Services\NotificationSettingService;
use Asantibanez\LaravelSubscribableNotifications\NotificationSubscriptionManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TelegramController extends Controller
{
    protected $bot;
    protected $modules;
    protected $user;
    protected $modName;
    protected $modDescription;
    protected $modAuthor;
    protected $commands;

    public function __construct()
    {
        $this->bot = new Client(env('TELEGRAM_BOT_TOKEN'));
        $this->modules = config('my-health-telegram-bot.modules');
        $this->modName = config('my-health-telegram-bot.name');
        $this->modDescription = config('my-health-telegram-bot.description');
        $this->commands = config('my-health-telegram-bot.commands');
    }

    public function handleUpdates()
    {
        $lastUpdateId = cache()->get('last_update_id', 0);
        $updates = $this->bot->getUpdates($lastUpdateId + 1);

        $storageUpdates = new TelegramUpdateCollection(cache()->get('telegram_updates') ?? []);
        $storageUpdateIds = cache()->get('telegram_storage_update_ids') ?? [];

        foreach ($updates as $update) {
            $updateId = $update->getUpdateId();

            $updateObj = new TelegramUpdateResource(json_decode($update->toJson(), true));
            $storageUpdates->push($updateObj);
            $storageUpdateIds[] = $updateId;
            if (!$update->getMessage()) {
                $resource = new TelegramUpdateResource(json_decode($update->toJson(), true));
                $resource = $resource->toArray(request());
                $chatId = $resource['chat_id'];
                $text = $resource['data'];
                $command = explode(':', $text)[0];
                if (!in_array($command, $this->commands)) {
                    cache()->put('last_update_id', $updateId);
                    continue;
                }
                cache()->put("chat_{$chatId}_state", 'execute_command');
            } else {
                $message = $update->getMessage();
                $chatId = $message->getChat()->getId();
                $text = $message->getText();
                $photo = $message->getPhoto();
            }

            $this->user = cache()->get("user_{$chatId}", null);

            if (!$this->user) {
                $this->user = User::where('telegram_user_id', $chatId)->first();
                if ($this->user) {
                    cache()->put("user_{$chatId}", $this->user);
                }
            }


            if ($text === '/menu' || $text === '/start') {
                cache()->put('last_update_id', $updateId);
                cache()->put('telegram_storage_update_ids', $storageUpdateIds);
                cache()->put('telegram_updates', $storageUpdates);
                if (!$this->user) {
                    $this->sendAnonymousMainMenu($chatId);
                    cache()->put("chat_{$chatId}_state", 'anonymous_main_menu');
                    return;
                }
                $this->sendMainMenu($chatId);
                cache()->put("chat_{$chatId}_state", 'main_menu');
                return;
            }

            $state = cache()->get("chat_{$chatId}_state", 'idle');

            if ($state === 'main_menu') {
                $this->handleMainMenuSelection($chatId, $text);
            } elseif ($state === 'anonymous_main_menu') {
                $this->handleAnonymousMainMenuSelection($chatId, $text);
            } elseif ($state === 'module_menu') {
                $moduleIndex = cache()->get("chat_{$chatId}_selected_module");
                $this->handleModuleSelection($chatId, $text, $moduleIndex);
            } elseif (preg_match('/handling_option_\d+_\d+_\d+/', $state) || preg_match('/optional_param_\d+_\d+_\d+/', $state)) {
                $this->handleParameterInput($chatId, $text, $photo);
            } elseif (strpos($state, 'register_') === 0) {
                $this->handleRegistration($chatId, $text, $photo);
            } elseif ($state === 'execute_command') {
                $this->executeCommand($resource);
            } else {
                $this->bot->sendMessage($chatId, "Comando não reconhecido. Por favor, envie /menu para ver as opções.");
            }

            cache()->put('last_update_id', $updateId);
        }
        cache()->put('telegram_storage_update_ids', $storageUpdateIds);
        cache()->put('telegram_updates', $storageUpdates);
    }

    protected function sendMainMenu($chatId)
    {
        $text = "Menu Principal:\n\n";
        foreach ($this->modules as $index => $module) {
            if ($module['enabled']) { // Adicionado para verificar se a opção está habilitada
                $text .= ($index + 1) . ". " . $module['title'] . "\n";
            }
        }
        $text .= "0. Sair";
        $this->bot->sendMessage($chatId, $text);
    }

    protected function sendAnonymousMainMenu($chatId, $welcome = true)
    {
        if ($welcome) {
            $user_name = $this->getTelegramProfile($chatId, 'firstName');
            if (!$user_name) {
                $user_name = 'Usuário';
            }
            $text = "Olá, {$user_name}! Bem-vindo ao {$this->modName}!\n\n";
            $this->bot->sendMessage($chatId, $text);

            sleep(1);
            $text = "Parece que você ainda não está cadastrado em nosso sistema.\nCadastre-se gratuitamente para começar a usar nossos serviços!\n\n";
            $this->bot->sendMessage($chatId, $text);

            sleep(1);
        }
        $text = "Para começar, por favor, selecione uma das opções abaixo:\n\n";
        $text .= "1. Cadastrar-se\n";
        $text .= "2. Sobre\n";
        $text .= "0. Sair";
        $this->bot->sendMessage($chatId, $text);
    }

    protected function sendModuleMenu($chatId, $moduleIndex, $showHeader = true)
    {
        $module = $this->modules[$moduleIndex];
        $text = $showHeader ? "{$module['title']}\n{$module['description']}\n\n" : "O que deseja fazer agora?\n\n";
        foreach ($module['options'] as $key => $option) {
            $text .= "{$key}. {$option['title']}\n";
        }
        $text .= "0. Voltar ao Menu Principal";
        $this->bot->sendMessage($chatId, $text);
        cache()->put("chat_{$chatId}_state", 'module_menu');
        cache()->put("chat_{$chatId}_selected_module", $moduleIndex);
    }

    protected function handleMainMenuSelection($chatId, $text)
    {
        if ($text === '0') {
            $this->exitMenu($chatId);
            return;
        }

        $index = (int) $text - 1;
        if (isset($this->modules[$index])) {
            $this->sendModuleMenu($chatId, $index, true);
        } else {
            $this->bot->sendMessage($chatId, "Opção inválida. Por favor, selecione uma opção válida.");
        }
    }

    protected function handleAnonymousMainMenuSelection($chatId, $text)
    {
        if ($text === '0') {
            $this->exitMenu($chatId);
            return;
        }

        if ($text === '1' && !$this->user) {
            $this->bot->sendMessage($chatId, "Vamos começar o seu cadastro!\nQual o seu nome completo?");
            cache()->put("chat_{$chatId}_state", 'register_name');
            return;
        }

        if ($text === '2') {
            $this->bot->sendMessage($chatId, "Sobre o {$this->modName}\n\n{$this->modDescription}");

            sleep(1);
            $this->sendAnonymousMainMenu($chatId, false);

            return;
        }

        $this->bot->sendMessage($chatId, "Opção inválida. Por favor, selecione uma opção válida.");

        return;
    }

    protected function handleModuleSelection($chatId, $text, $moduleIndex)
    {
        if ($text === '0') {
            $this->sendMainMenu($chatId);
            cache()->put("chat_{$chatId}_state", 'main_menu');
            cache()->forget("chat_{$chatId}_selected_module");

            return;
        }

        $option = (int) $text;
        $module = $this->modules[$moduleIndex];
        if (isset($module['options'][$option])) {
            $optionData = $module['options'][$option];
            $this->bot->sendMessage($chatId, $optionData['title']);

            $params = [];
            $firstParamIndex = 0;
            foreach ($optionData['params'] as $index => $paramData) {
                if ($paramData['get_value_from'] !== 'response') {
                    $params[$paramData['var_name']] = $this->getAutomaticValue($paramData, $chatId);
                    $firstParamIndex = $index + 1;
                } else {
                    break;
                }
            }

            cache()->put("chat_{$chatId}_params", $params);

            if (isset($optionData['params'][$firstParamIndex])) {
                $firstParam = $optionData['params'][$firstParamIndex];
                if ($firstParam['required']) {
                    if ($firstParam['question']) {
                        if (strpos($paramData['question'], 'function') !== false) {
                            $function = $this->extractFunctionQuestion($paramData['question']);
                            $functionParams = $this->extractFunctionQuestionParams($paramData['question']);
                            if ($this->executeOptionQuestion($chatId, $module['service'], $function, $functionParams, $optionData) == false) {
                                return;
                            }
                            cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$option}_{$firstParamIndex}");
                            return;
                        }
                        $this->bot->sendMessage($chatId, $firstParam['question']);
                    } else {
                        $this->bot->sendMessage($chatId, 'Qual o valor de ' . $firstParam['var_caption'] . '?');
                    }
                    cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$option}_{$firstParamIndex}");
                } else {
                    $this->bot->sendMessage($chatId, "Deseja fornecer " . $firstParam['var_caption'] . "? (S/N)");
                    cache()->put("chat_{$chatId}_state", "optional_param_{$moduleIndex}_{$option}_{$firstParamIndex}");
                }
            } else {
                $this->executeOptionFunction($chatId, $module['service'], $optionData['function'], $params, $optionData);
                $this->sendModuleMenu($chatId, $moduleIndex, false);
            }
        } else {
            $this->bot->sendMessage($chatId, "Opção inválida. Por favor, selecione uma opção válida.");
        }
    }

    protected function handleParameterInput($chatId, $text, $photo = null)
    {
        $state = cache()->get("chat_{$chatId}_state");

        if (strpos($state, 'register_') === 0) {
            $this->handleRegistration($chatId, $text);
            return;
        }

        preg_match('/handling_option_(\d+)_(\d+)_(\d+)/', $state, $matches);
        preg_match('/optional_param_(\d+)_(\d+)_(\d+)/', $state, $optionalMatches);

        if (!$matches && !$optionalMatches) {
            $this->bot->sendMessage($chatId, "Ocorreu um erro ao processar a sua entrada.");
            return;
        }

        if ($optionalMatches) {
            list(, $moduleIndex, $optionIndex, $paramIndex) = $optionalMatches;
            if (strtolower($text) === 's' || strtolower($text) === 'sim' || strtolower($text) === 'y' || strtolower($text) === 'yes') {
                // Perguntar pelo valor do parâmetro opcional
                $paramData = $this->modules[$moduleIndex]['options'][$optionIndex]['params'][$paramIndex];
                if (strpos($paramData['question'], 'function') !== false) {
                    $function = $this->extractFunctionQuestion($paramData['question']);
                    $functionParams = $this->extractFunctionQuestionParams($paramData['question']);
                    if ($this->executeOptionQuestion($chatId, $this->modules[$moduleIndex]['service'], $function, $functionParams, $this->modules[$moduleIndex]['options'][$optionIndex]) == false) {
                        return;
                    }
                    cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$optionIndex}_{$paramIndex}");
                    return;
                }
                $this->bot->sendMessage($chatId, $paramData['question']);
                cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$optionIndex}_{$paramIndex}");
            } else {
                // Pular o parâmetro opcional
                $params = cache()->get("chat_{$chatId}_params", []);
                $nextParamIndex = $paramIndex + 1;
                $this->handleNextParameterOrExecute($chatId, $moduleIndex, $optionIndex, $nextParamIndex, $params);
            }
            return;
        }

        list(, $moduleIndex, $optionIndex, $paramIndex) = $matches;
        $module = $this->modules[$moduleIndex];
        $option = $module['options'][$optionIndex];

        $params = cache()->get("chat_{$chatId}_params", []);
        $paramData = $option['params'][$paramIndex];

        if ($paramData['get_value_from'] === 'response') {
            $value = $this->validateParam($paramData, $text, $photo);
            if ($value === false) {
                if (strpos($paramData['error_message'], 'function') !== false) {
                    $function = $this->extractFunctionQuestion($paramData['error_message']);
                    $functionParams = $this->extractFunctionQuestionParams($paramData['error_message']);
                    $this->executeOptionQuestion($chatId, $module['service'], $function, $functionParams, $option);
                    return;
                }
                $this->bot->sendMessage($chatId, $paramData['error_message']);
                return;
            }
            $params[$paramData['var_name']] = $value;
        } else {
            $params[$paramData['var_name']] = $this->getAutomaticValue($paramData, $chatId);
        }

        cache()->put("chat_{$chatId}_params", $params);

        $nextParamIndex = $paramIndex + 1;
        $this->handleNextParameterOrExecute($chatId, $moduleIndex, $optionIndex, $nextParamIndex, $params);
    }

    protected function handleNextParameterOrExecute($chatId, $moduleIndex, $optionIndex, $paramIndex, $params)
    {
        $module = $this->modules[$moduleIndex];
        $option = $module['options'][$optionIndex];

        while (isset($option['params'][$paramIndex]) && $option['params'][$paramIndex]['get_value_from'] !== 'response') {
            $params[$option['params'][$paramIndex]['var_name']] = $this->getAutomaticValue($option['params'][$paramIndex], $chatId);
            $paramIndex++;
        }

        if (isset($option['params'][$paramIndex])) {
            $nextParam = $option['params'][$paramIndex];
            if ($nextParam['required']) {
                //check if the question have key word function
                if (strpos($nextParam['question'], 'function') !== false) {
                    $function = $this->extractFunctionQuestion($nextParam['question']);
                    $functionParams = $this->extractFunctionQuestionParams($nextParam['question']);
                    if ($this->executeOptionQuestion($chatId, $module['service'], $function, $functionParams, $option) == false) {
                        return;
                    }
                    //tag with prefix handling_option_chatId to indicate that the next state is handling_option
                    cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$optionIndex}_{$paramIndex}");
                    return;
                }
                $this->bot->sendMessage($chatId, $nextParam['question']);
                cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$optionIndex}_{$paramIndex}");
            } else {
                $this->bot->sendMessage($chatId, "Deseja fornecer " . $nextParam['var_caption'] . "? (S/N)");
                cache()->put("chat_{$chatId}_state", "optional_param_{$moduleIndex}_{$optionIndex}_{$paramIndex}");
            }
        } else {
            $this->executeOptionFunction($chatId, $module['service'], $option['function'], $params, $option);
            cache()->forget("chat_{$chatId}_params");
            cache()->forget("chat_{$chatId}_state");
            $this->sendModuleMenu($chatId, $moduleIndex, false);
        }
    }

    protected function extractFunctionQuestion($text)
    {
        $function = explode('function', $text);
        $function = explode('(', $function[1]);
        $function = $function[0];
        $function = trim($function);
        $function = explode(' ', $function);

        return $function[0];
    }

    protected function extractFunctionQuestionParams($text)
    {
        $functionParams = explode('(', $text);
        $functionParams = explode(')', $functionParams[1]);
        $functionParams = $functionParams[0];
        $functionParams = explode(',', $functionParams);
        $functionParams = array_map('trim', $functionParams);

        return $functionParams;
    }

    protected function handleRegistration($chatId, $text, $photo = null)
    {
        $state = cache()->get("chat_{$chatId}_state");
        $registrationData = cache()->get("chat_{$chatId}_registration_data", []);

        if (strtolower($text) === 'cancel' || strtolower($text) === 'cancelar') {
            $this->cancelRegistration($chatId);
            return;
        }

        if ($state === 'register_name') {
            // Validação do nome completo
            if (!$this->validateParam(['var_type' => 'full_name'], $text)) {
                $this->bot->sendMessage($chatId, "Nome inválido. Por favor, insira seu nome completo com pelo menos duas palavras e dois caracteres cada.");
                return;
            }

            $registrationData['name'] = $text;
            $this->bot->sendMessage($chatId, "Qual o seu email?");
            cache()->put("chat_{$chatId}_state", 'register_email');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_email') {
            // Validação do email
            if (!$this->validateParam(['var_type' => 'email'], $text)) {
                $this->bot->sendMessage($chatId, "Email inválido. Por favor, insira um email válido.");
                return;
            }

            $registrationData['email'] = $text;
            $this->bot->sendMessage($chatId, "Escolha uma senha. A senha deve conter no mínimo 6 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial.");
            cache()->put("chat_{$chatId}_state", 'register_password');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_password') {
            // Validação da senha
            if (!$this->validateParam(['var_type' => 'password'], $text)) {
                $this->bot->sendMessage($chatId, "Senha inválida. A senha deve conter no mínimo 6 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial.");
                return;
            }

            $registrationData['password'] = Hash::make($text);

            $registrationData['telegram_user_id'] = $chatId;
            $registrationData['telegram_user_deeplink'] = Uuid::uuid4();
            $this->user = User::create($registrationData);

            if ($this->user) {
                cache()->put("user_{$chatId}", $this->user);
                $name = ucfirst(explode(' ', $registrationData['name'])[0]);
                $this->bot->sendMessage($chatId, "{$name}, seu cadastro foi concluído com sucesso! A partir de agora você já pode usufruir de todos os recursos do sistema.");
                $this->sendMainMenu($chatId);
                cache()->put("chat_{$chatId}_state", 'main_menu');
                cache()->forget("chat_{$chatId}_registration_data");
                return;
            }

            $this->bot->sendMessage($chatId, "Tivemos algum problem ao salvar seu cadastro. Por favor, tente novamente.");
            $this->sendMainMenu($chatId);
            cache()->put("chat_{$chatId}_state", 'main_menu');
            cache()->forget("chat_{$chatId}_registration_data");
            return;

            // $this->bot->sendMessage($chatId, "Deseja receber notificações via Telegram? (S/N)");
            // cache()->put("chat_{$chatId}_state", 'register_notifications');
            // cache()->put("chat_{$chatId}_registration_data", $registrationData);
            // return;
        }

        if ($state === 'register_notifications') {
            // Validação das notificações
            if (!in_array(strtoupper($text), ['S', 'N'])) {
                $this->bot->sendMessage($chatId, "Opção inválida. Por favor, insira S ou N.");
                return;
            }

            $registrationData['notifications'] = strtoupper($text) === 'S';

            if ($registrationData['notifications']) {
                $subscribeManagement = new NotificationSubscriptionManager();
                $subscribableNotifications = $subscribeManagement->subscribableNotifications();
                foreach ($subscribableNotifications as $subscribableNotification) {
                    $subscribeManagement->subscribe($this->user, $subscribableNotification);
                }
                $notificationSettingService = new NotificationSettingService(new NotificationSettingRepository());
                $payload = config('water-intake-reminder.default_notification_setting');
                $payload['user_id'] = $this->user->id;
                $payload['type'] = 'water-intake-reminder-telegram';
                $notificationSettingService->create($payload);
                $payload['type'] = 'water-intake-reminder-database';
                $notificationSettingService->create($payload);
            }
        }
    }

    protected function cancelRegistration($chatId)
    {
        $this->bot->sendMessage($chatId, "Cadastro cancelado. Para se cadastrar, envie /menu e selecione a opção Cadastrar-se.");
        cache()->forget("chat_{$chatId}_state");
        cache()->forget("chat_{$chatId}_registration_data");
        return;
    }

    protected function getTelegramProfile($chatId, $field)
    {
        $profile = $this->bot->getChat($chatId);
        $fieldValue = $profile->{'get' . ucfirst($field)}();
        if ($profile && $fieldValue) {
            return $fieldValue;
        }

        return null;
    }

    protected  function getUserProfilePhotos($chatId)
    {
        $userProfilePhotos = $this->bot->getUserProfilePhotos($chatId);
        $photos = $userProfilePhotos->getPhotos();
        if (count($photos) > 0) {
            $photo = $photos[0][count($photos[0]) - 1];
            $fileId = $photo->getFileId();
            $file = $this->bot->getFile($fileId);
            $filePath = $file->getFilePath();
            $photoUrl = $this->bot->getFileUrl($filePath);

            return $photoUrl . '/' . $filePath;
        }

        return null;
    }

    protected  function getUserSentPhotos($photos)
    {
        if (count($photos) > 0) {
            $photo = $photos[count($photos) - 1];
            $fileId = $photo->getFileId();
            $file = $this->bot->getFile($fileId);
            $filePath = $file->getFilePath();
            $photoUrl = $this->bot->getFileUrl($filePath);

            return $photoUrl . '/' . $filePath;
        }

        return null;
    }

    protected function savePhoto($photo)
    {
        if ($photo) {
            $contents = file_get_contents($photo);
            $extension = pathinfo($photo, PATHINFO_EXTENSION);
            $extension = $extension == 'jpeg' ? 'jpg' : $extension;
            $name = md5(Carbon::now()) . '.' . $extension;
            $filePath = 'images/users/' . $name;
            Storage::disk('public')->put($filePath, $contents);

            return $filePath;
        }

        return null;
    }

    protected function getAutomaticValue($paramData)
    {
        if ($paramData['var_name'] === 'user_id') {
            return $this->user->id;
        }

        if (isset($this->{$paramData['get_value_from']})) {
            if ($paramData['var_type'] !== 'model') {
                if (is_array($this->{$paramData['get_value_from']})) {
                    return $this->{$paramData['get_value_from']}[$paramData['var_name']];
                }
                return $this->{$paramData['get_value_from']}->{$paramData['var_name']};
            } else {
                $modelClass = '\\App\\Models\\' . ucfirst($paramData['get_value_from']);
                $model = new $modelClass();
                if ($this->{$paramData['get_value_from']} instanceof $model) {
                    return $this->{$paramData['get_value_from']};
                }
            }
            return null;
        }

        // Adicione outras lógicas de obtenção automática aqui conforme necessário
        return null;
    }

    protected function validateParam($param, $text, $photo = null)
    {
        if ($param['var_type'] === 'int') {
            return filter_var($text, FILTER_VALIDATE_INT) !== false ? (int) $text : false;
        } elseif ($param['var_type'] === 'string') {
            return is_string($text) ? $text : false;
        } elseif ($param['var_type'] === 'date') {
            $date = \DateTime::createFromFormat('d/m/Y', $text);
            return $date && $date->format('d/m/Y') === $text ? $text : false;
        } elseif ($param['var_type'] === 'time') {
            $time = \DateTime::createFromFormat('H:i', $text);
            return $time && $time->format('H:i') === $text ? $text : false;
        } elseif ($param['var_type'] === 'email') {
            $isValid = filter_var($text, FILTER_VALIDATE_EMAIL);
            $isUnique = User::where('email', $text)->count() === 0;
            return $isValid && $isUnique ? $text : false;
        } elseif ($param['var_type'] === 'phone') {
            return preg_match('/^\d{10,11}$/', $text) ? $text : false;
        } elseif ($param['var_type'] === 'full_name') {
            return preg_match('/^([a-zA-ZÀ-ÿ]{2,}(\s+[a-zA-ZÀ-ÿ]{2,})+)$/u', trim($text)) ? $text : false;
        } elseif ($param['var_type'] === 'password') {
            return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $text) ? $text : false;
        } elseif ($param['var_type'] === 'birthday') {
            return preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $text) && strtotime($text) < strtotime('-14 years') ? $text : false;
        } elseif ($param['var_type'] === 'gender') {
            return in_array(strtoupper($text), ['M', 'F']) ? strtoupper($text) : false;
        } elseif ($param['var_type'] === 'activity_level') {
            return in_array((int)$text, [1, 2, 3, 4, 5]) ? (int)$text : false;
        } elseif ($param['var_type'] === 'photo') {
            $photo = $this->getUserSentPhotos($photo);
            $photo = $this->savePhoto($photo);
            return $photo ? $photo : false;
        } elseif ($param['var_type'] === 'model') {
            $modelClass = '\\App\\Models\\' . ucfirst($param['get_value_from']);
            $model = new $modelClass();
            $model = $model->find($text);
            return $model ? $model : false;
        } elseif ($param['var_type'] === 'physical_activity_category') {
            $serviceClass = '\\App\\Services\\PhysicalActivityService';
            $repository = '\\App\\Repositories\\PhysicalActivityRepository';
            $serviceInstance = new $serviceClass(new $repository);
            $categories = $serviceInstance->getCategoryOptions();
            $categoryIds = array_map(function ($category) {
                return $category['id'];
            }, $categories->toArray(request()));
            return in_array((int)$text, $categoryIds) ? (int)$text : false;
        } elseif ($param['var_type'] === 'physical_activity_sport') {
            $serviceClass = '\\App\\Services\\PhysicalActivityService';
            $repository = '\\App\\Repositories\\PhysicalActivityRepository';
            $serviceInstance = new $serviceClass(new $repository);
            $category_id = cache()->get("chat_{$this->user->telegram_user_id}_params")['category_id'];
            $sports = $serviceInstance->getSportOptions($category_id);
            $sportIds = array_map(function ($sport) {
                return $sport['id'];
            }, $sports->toArray(request()));
            return in_array((int)$text, $sportIds) ? (int)$text : false;
        } elseif ($param['var_type'] === 'effort') {
            $serviceClass = '\\App\\Services\\PhysicalActivityService';
            $repository = '\\App\\Repositories\\PhysicalActivityRepository';
            $serviceInstance = new $serviceClass(new $repository);
            $effortLevelsDb = $serviceInstance->getEffortLevels(true);
            return in_array((int)$text, array_keys($effortLevelsDb)) ? $effortLevelsDb[(int)$text] : false;
        }

        return false;
    }

    protected function executeOptionFunction($chatId, $service, $function, $params, $optionData)
    {
        $serviceClass = '\\App\\Services\\' . $service . 'Service';
        $repository = '\\App\\Repositories\\' . $service . 'Repository';

        try {
            $serviceInstance = new $serviceClass(new $repository);

            if (count($params) === 1) {
                $params = array_values($params)[0];
            }

            $result = $serviceInstance->$function($params);

            if ($result) {
                if (isset($optionData['return_type']) && $optionData['return_type'] == 'message') {
                    $this->bot->sendMessage($chatId, $optionData['return_message']);
                } elseif (isset($optionData['return_type']) && $optionData['return_type'] == 'result') {
                    if (is_array($result)) {
                        foreach ($result as $res) {
                            $res = preg_replace('/([._`[\]()~>#+\-=|{}!])/m', '\\\\$1', $result);
                            sleep(1);
                            $this->bot->sendMessage($chatId, $res, 'MarkdownV2');
                        }
                    } else {
                        $result = preg_replace('/([._`[\]()~>#+\-=|{}!])/m', '\\\\$1', $result);
                        $this->bot->sendMessage($chatId, $result, 'MarkdownV2');
                    }
                } else {
                    $this->bot->sendMessage($chatId, "Ação realizada com sucesso.");
                }
            } else {
                $this->bot->sendMessage($chatId, "Houve um problema ao realizar a ação.");
            }
        } catch (\Exception $e) {
            Log::error($e);
            $this->bot->sendMessage($chatId, "Houve um problema ao realizar a ação. Erro: " . $e->getMessage());
        }
    }

    protected function executeOptionQuestion($chatId, $service, $function, $params, $optionData)
    {
        $serviceClass = '\\App\\Services\\' . $service . 'Service';
        $repository = '\\App\\Repositories\\' . $service . 'Repository';
        $storedParams = cache()->get("chat_{$chatId}_params", []);

        try {
            $serviceInstance = new $serviceClass(new $repository);
            foreach ($params as $key => $param) {
                if (isset($storedParams[$param])) {
                    $params[$key] = $storedParams[$param];
                }
            }
            if (count($params) === 1) {
                $params = !empty(array_values($params)[0]) ? array_values($params)[0] : null;
            }
            $result = $serviceInstance->$function($params);

            if ($result) {
                //check if result is a array, if true, get result message as message text and result options to create replay markup
                if (is_array($result)) {
                    if (isset($result['error'])) {
                        $this->bot->sendMessage($chatId, $result['error']);
                        sleep(1);
                        $this->sendModuleMenu($chatId, cache()->get("chat_{$chatId}_selected_module"), false);
                        cache()->put("chat_{$chatId}_state", 'module_menu');
                        return false;
                    } else {
                        $message = array_shift($result);
                        $options = $result;
                        $keyboard = new InlineKeyboardMarkup($options['inline_keyboard'], true, true, false);
                        $message = preg_replace('/([._`[\]()~>#+\-=|{}!])/m', '\\\\$1', $message);
                        $this->bot->sendMessage($chatId, $message, 'MarkdownV2', false, null, $keyboard);
                    }
                } else {
                    $result = preg_replace('/([._`[\]()~>#+\-=|{}!])/m', '\\\\$1', $result);
                    $this->bot->sendMessage($chatId, $result, 'MarkdownV2');
                }
            } else {
                $this->bot->sendMessage($chatId, "Houve um problema ao realizar a ação.");
            }
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            $this->bot->sendMessage($chatId, "Houve um problema ao realizar a ação. Erro: " . $e->getMessage());
        }
    }

    protected function exitMenu($chatId)
    {
        $this->bot->sendMessage($chatId, "Você saiu do menu! Até a próxima.\n\nSempre que quiser ativar o menu novamente, basta enviar /menu que ele será ativado.");
        cache()->forget("chat_{$chatId}_state");
        cache()->forget("user_{$chatId}");
        cache()->forget("chat_{$chatId}_registration_data");
        cache()->forget("chat_{$chatId}_params");
        cache()->forget("chat_{$chatId}_selected_module");

        return;
    }

    protected function executeCommand($resource)
    {
        $updateId = $resource['update_id'];
        $chatId = $resource['chat_id'];
        $serviceName = $resource['command']['service'];
        $function = $resource['command']['function'];
        $field = $resource['command']['field'];
        $value = $resource['command']['value'];

        $module = cache()->get("chat_{$chatId}_selected_module");

        if ($value === 'cancel') {
            $this->bot->sendMessage($chatId, "Operação cancelada.");
            cache()->forget("chat_{$chatId}_state");
            if (!$module) {
                return;
            }
            sleep(1);
            $this->sendModuleMenu($chatId, $module, false);
            return;
        }

        $service = '\\App\\Services\\' . ucfirst($serviceName) . 'Service';
        $repository = '\\App\\Repositories\\' . ucfirst($serviceName) . 'Repository';

        $payload = [
            'user_id' => $this->user->id,
            $field => $value
        ];

        $serviceInstance = new $service(new $repository);
        $object = $serviceInstance->$function($payload);

        if ($object) {
            //return back a message to user telegram chat saying that the operation was successful
            $this->bot->sendMessage($chatId, "Operação realizada com sucesso.");

            Log::info('User with ID: ' . $this->user->id . ' has updated ' . $serviceName . ' with ' . $field . ' = ' . $value . ' received from Telegram Bot Callback update id: ' . $updateId);
        } else {
            Log::error('User with ID: ' . $this->user->id . ' has tried to update ' . $serviceName . ' with ' . $field . ' = ' . $value . ' received from Telegram Bot Callback update id: ' . $updateId);
        }
        cache()->forget("chat_{$chatId}_state");
        if (!$module) {
            return;
        }
        sleep(1);
        $this->sendModuleMenu($chatId, $module, false);
        return;
    }
}
