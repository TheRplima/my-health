<?php

namespace App\Http\Controllers;

use App\Http\Resources\TelegramUpdateCollection;
use App\Http\Resources\TelegramUpdateResource;
use App\Models\User;
use Asantibanez\LaravelSubscribableNotifications\NotificationSubscriptionManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use TelegramBot\Api\Client;

class TelegramController extends Controller
{
    protected $bot;
    protected $modules;
    protected $user;

    public function __construct()
    {
        $this->bot = new Client(env('TELEGRAM_BOT_TOKEN'));
        $this->modules = [
            [
                'title' => 'Controle de ingestão de água',
                'description' => 'Mantenha-se hidratado registrando diariamente o seu consumo de água.',
                'service' => 'WaterIntake',
                'options' => [
                    1 => [
                        'title' => 'Registrar quantidade de água ingerida',
                        'function' => 'create',
                        'return_type' => 'message',
                        'return_message' => 'Consumo de água registrado com sucesso.',
                        'params' => [
                            [
                                'var_name' => 'user_id',
                                'var_caption' => 'Usuário',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'user'
                            ],
                            [
                                'var_name' => 'amount',
                                'var_caption' => 'Quantidade de água',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => 'Qual a quantidade de água ingerida (em ml)?',
                                'error_message' => 'Quantidade inválida. Qual a quantidade de água ingerida (em ml)?',
                                'get_value_from' => 'response'
                            ],
                        ]
                    ],
                    2 => [
                        'title' => 'Ver consumo de hoje',
                        'function' => 'showWaterIntakeToday',
                        'return_type' => 'result',
                        'return_message' => null,
                        'params' => [
                            [
                                'var_name' => 'user',
                                'var_caption' => 'Usuário',
                                'var_type' => 'model',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'user'
                            ],
                        ]
                    ],
                ]
            ],
            [
                'title' => 'Controle de Peso',
                'description' => 'Mantenha-se saudável registrando periodicamente o seu peso e acompanhando sua evolução.',
                'service' => 'WeightControl',
                'options' => [
                    1 => [
                        'title' => 'Registrar peso',
                        'function' => 'create',
                        'return_type' => 'message',
                        'return_message' => 'Peso registrado com sucesso.',
                        'params' => [
                            [
                                'var_name' => 'user_id',
                                'var_caption' => 'Usuário',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                            [
                                'var_name' => 'weight',
                                'var_caption' => 'Peso',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => 'Qual o seu peso (em kg)?',
                                'error_message' => 'Peso inválido. Qual o seu peso (em kg)?',
                                'get_value_from' => 'response'
                            ],
                            [
                                'var_name' => 'date',
                                'var_caption' => 'Data da medição',
                                'var_type' => 'date',
                                'required' => false,
                                'question' => 'Qual a data da medição (DD/MM/YYYY)?',
                                'error_message' => 'Data inválida. Qual a data da medição (DD/MM/YYYY)?',
                                'get_value_from' => 'response'
                            ]
                        ]
                    ],
                    2 => [
                        'title' => 'Ver peso do mês',
                        'function' => 'showWeightForThisMonth',
                        'return_type' => 'result',
                        'return_message' => null,
                        'params' => [
                            [
                                'var_name' => 'user',
                                'var_caption' => 'Usuário',
                                'var_type' => 'model',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'user'
                            ],
                        ]
                    ],
                    3 => [
                        'title' => 'Ver peso do ano',
                        'function' => 'showWeightForThisYear',
                        'return_type' => 'result',
                        'return_message' => null,
                        'params' => [
                            [
                                'var_name' => 'user',
                                'var_caption' => 'Usuário',
                                'var_type' => 'model',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'user'
                            ],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Recipientes de Água',
                'description' => 'Gerencie seus recipientes de água facilitando o registro do consumo diário.',
                'service' => 'WaterIntakeContainer',
                'options' => [
                    1 => [
                        'title' => 'Cadastrar recipiente',
                        'function' => 'create',
                        'return_type' => 'message',
                        'return_message' => 'Recipiente cadastrado com sucesso.',
                        'params' => [
                            [
                                'var_name' => 'user_id',
                                'var_caption' => 'Usuário',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                            [
                                'var_name' => 'name',
                                'var_caption' => 'Nome do recipiente',
                                'var_type' => 'string',
                                'required' => true,
                                'question' => 'Qual o nome do recipiente?',
                                'error_message' => 'Nome inválido. Qual o nome do recipiente?',
                                'get_value_from' => 'response'
                            ],
                            [
                                'var_name' => 'size',
                                'var_caption' => 'Capacidade do recipiente',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => 'Qual capacidade do recipiente? (em ml)',
                                'error_message' => 'Capacidade inválida. Qual capacidade do recipiente? (em ml)',
                                'get_value_from' => 'response'
                            ],
                        ]
                    ],
                    2 => [
                        'title' => 'Ver recipientes cadastrados',
                        'function' => 'showWaterIntakeContainers',
                        'return_type' => 'result',
                        'return_message' => null,
                        'params' => [
                            [
                                'var_name' => 'user',
                                'var_caption' => 'Usuário',
                                'var_type' => 'model',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'user'
                            ],
                        ]
                    ]
                ]
            ],
            [
                'title' => 'Controle de notificações',
                'description' => 'Gerencie suas notificações de forma simples e rápida.',
                'service' => 'NotificationSetting',
                'options' => [
                    1 => [
                        'title' => 'Ativar notificações',
                        'function' => 'enableAllFromUser',
                        'return_type' => 'message',
                        'return_message' => 'Notificações ativadas com sucesso.',
                        'params' => [
                            [
                                'var_name' => 'user_id',
                                'var_caption' => 'Usuário',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ]
                        ]
                    ],
                    2 => [
                        'title' => 'Desativar notificações',
                        'function' => 'disableAllFromUser',
                        'return_type' => 'message',
                        'return_message' => 'Notificações desativadas com sucesso.',
                        'params' => [
                            [
                                'var_name' => 'user_id',
                                'var_caption' => 'Usuário',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                        ]
                    ],
                    3 => [
                        'title' => 'Suspender notificações',
                        'function' => 'snoozeAllFromUser',
                        'return_type' => 'message',
                        'return_message' => 'Notificações suspensas com sucesso.',
                        'params' => [
                            [
                                'var_name' => 'user_id',
                                'var_caption' => 'Usuário',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                            [
                                'var_name' => 'minutes',
                                'var_caption' => 'Minutos',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => 'Por quantos minutos deseja suspender as notificações?',
                                'error_message' => 'Valor inválido. Por quantos minutos deseja suspender as notificações?',
                                'get_value_from' => 'response'
                            ]
                        ]
                    ]
                ]
            ],
            // [
            //     'title' => 'Controle de Atividades Físicas',
            //     'description' => 'Registre suas atividades físicas diárias e acompanhe sua evolução.',
            //     'service' => 'PhysicalActivity',
            //     'options' => [
            //         1 => [
            //             'title' => 'Registrar atividade física',
            //             'function' => 'create',
            //             'return_type' => 'message',
            //             'return_message' => 'Atividade física registrada com sucesso.',
            //             'params' => [
            //                 [
            //                     'var_name' => 'user_id',
            //                     'var_type' => 'int',
            //                     'required' => true,
            //                     'question' => null,
            //                     'error_message' => null,
            //                     'get_value_from' => 'system'
            //                 ],
            //                 [
            //                     'var_name' => 'activity',
            //                     'var_type' => 'string',
            //                     'required' => true,
            //                     'question' => 'Qual a atividade realizada?',
            //                     'error_message' => 'Atividade inválida. Qual a atividade realizada?',
            //                     'get_value_from' => 'response'
            //                 ],
            //                 [
            //                     'var_name' => 'duration',
            //                     'var_type' => 'int',
            //                     'required' => true,
            //                     'question' => 'Qual a duração da atividade (em minutos)?',
            //                     'error_message' => 'Duração inválida. Qual a duração da atividade (em minutos)?',
            //                     'get_value_from' => 'response'
            //                 ],
            //                 [
            //                     'var_name' => 'date',
            //                     'var_type' => 'date',
            //                     'required' => false,
            //                     'question' => 'Qual a data da atividade (DD/MM/YYYY)?',
            //                     'error_message' => 'Data inválida. Qual a data da atividade (DD/MM/YYYY)?',
            //                     'get_value_from' => 'response'
            //                 ]
            //             ]
            //         ],
            //         2 => [
            //             'title' => 'Ver atividades físicas',
            //             'function' => 'showPhysicalActivitiesForThisMonth',
            //             'return_type' => 'result',
            //             'return_message' => null,
            //             'params' => [
            //                 [
            //                     'var_name' => 'user',
            //                     'var_type' => 'model',
            //                     'required' => true,
            //                     'question' => null,
            // ]
        ];
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
                cache()->put('last_update_id', $updateId);
                continue;
            }

            $message = $update->getMessage();
            $text = $message->getText();
            $chatId = $message->getChat()->getId();

            $this->user = cache()->get("user_{$chatId}", null);

            if (!$this->user) {
                $this->user = User::where('telegram_user_id', $chatId)->first();
                if ($this->user) {
                    cache()->put("user_{$chatId}", $this->user);
                }
            }


            if ($text === '/menu') {
                $this->sendMainMenu($chatId);
                cache()->put("chat_{$chatId}_state", 'main_menu');
                cache()->put('last_update_id', $updateId);
                cache()->put('telegram_storage_update_ids', $storageUpdateIds);
                cache()->put('telegram_updates', $storageUpdates);
                return;
            }

            $state = cache()->get("chat_{$chatId}_state", 'idle');

            if ($state === 'main_menu') {
                $this->handleMainMenuSelection($chatId, $text);
            } elseif ($state === 'module_menu') {
                $moduleIndex = cache()->get("chat_{$chatId}_selected_module");
                $this->handleModuleSelection($chatId, $text, $moduleIndex);
            } elseif (preg_match('/handling_option_\d+_\d+_\d+/', $state) || preg_match('/optional_param_\d+_\d+_\d+/', $state)) {
                $this->handleParameterInput($chatId, $text);
            } elseif (strpos($state, 'register_') === 0) {
                $this->handleRegistration($chatId, $text);
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
        if (!$this->user) {
            $text = "Olá! Seja bem vindo!\nParece que você ainda não está cadastrado em nosso sistema.\nPara se cadastrar, acesse a opção Cadastrar-se no menu principal.";
            $this->bot->sendMessage($chatId, $text);
            $text = "Menu Principal:\n\n";
            $text .= "C. Cadastrar-se\n";
            $text .= "0. Sair";
            $this->bot->sendMessage($chatId, $text);
            return;
        }

        $text = "Menu Principal:\n\n";
        foreach ($this->modules as $index => $module) {
            $text .= ($index + 1) . ". " . $module['title'] . "\n";
        }
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
            $this->bot->sendMessage($chatId, "Você saiu do menu! Até a próxima.\n\nSempre que quiser ativar o menu novamente, basta enviar /menu que ele será ativado.");
            cache()->forget("chat_{$chatId}_state");
            return;
        }

        if (strtolower($text) === 'c' || strtolower($text) === 'cadastrar' || strtolower($text) === 'cadastrar-se' || strtolower($text) === 'cadastro' || strtolower($text) === 'register') {
            $this->bot->sendMessage($chatId, "Vamos começar o seu cadastro!\nQual o seu nome completo?");
            cache()->put("chat_{$chatId}_state", 'register_name');
            return;
        }

        $index = (int) $text - 1;
        if (isset($this->modules[$index])) {
            $this->sendModuleMenu($chatId, $index, true);
        } else {
            $this->bot->sendMessage($chatId, "Opção inválida. Por favor, selecione uma opção válida.");
        }
    }

    protected function handleModuleSelection($chatId, $text, $moduleIndex)
    {
        if ($text === '0') {
            $this->sendMainMenu($chatId);
            cache()->put("chat_{$chatId}_state", 'main_menu');
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

    protected function handleParameterInput($chatId, $text)
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
            $value = $this->validateParam($paramData, $text);
            if ($value === false) {
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

    protected function handleRegistration($chatId, $text)
    {
        $state = cache()->get("chat_{$chatId}_state");
        $registrationData = cache()->get("chat_{$chatId}_registration_data", []);

        if (strtolower($text) === 'cancel' || strtolower($text) === 'cancelar') {
            $this->cancelRegistration($chatId);
            return;
        }

        if ($state === 'register_name') {
            // Validação do nome completo
            if (!preg_match('/^[a-zA-Z]{3,}\s[a-zA-Z]{3,}/', $text)) {
                $this->bot->sendMessage($chatId, "Nome inválido. Por favor, insira seu nome completo com pelo menos duas palavras e três caracteres cada.");
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
            if (!filter_var($text, FILTER_VALIDATE_EMAIL)) {
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
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $text)) {
                $this->bot->sendMessage($chatId, "Senha inválida. A senha deve conter no mínimo 6 caracteres, uma letra maiúscula, uma letra minúscula, um número e um caractere especial.");
                return;
            }

            $registrationData['password'] = Hash::make($text);
            $this->bot->sendMessage($chatId, "Qual a sua data de nascimento? (DD/MM/YYYY) [Opcional] - Envie 'pular' para ignorar.");
            cache()->put("chat_{$chatId}_state", 'register_dob');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_dob') {
            if (strtolower($text) === 'pular') {
                $registrationData['dob'] = null;
                $this->bot->sendMessage($chatId, "Qual o seu telefone? (Formato: DDD+Número, ex: 11987654321) [Opcional] - Envie 'pular' para ignorar.");
                cache()->put("chat_{$chatId}_state", 'register_phone');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação do telefone
            if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $text) || strtotime($text) > strtotime('-14 years')) {
                $this->bot->sendMessage($chatId, "Data de nascimento inválida. Por favor, insira uma data de nascimento válida (DD/MM/YYYY).");
                return;
            }

            //converter data para formato ingles yyyy-mm-dd e salvar
            $registrationData['dob'] = Carbon::createFromFormat('d/m/Y', $text)->format('Y-m-d');
            $this->bot->sendMessage($chatId, "Qual o seu telefone? (Formato: DDD+Número, ex: 11987654321) [Opcional] - Envie 'pular' para ignorar.");
            cache()->put("chat_{$chatId}_state", 'register_phone');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_phone') {
            if (strtolower($text) === 'pular') {
                $registrationData['phone'] = null;
                $this->bot->sendMessage($chatId, "Qual o seu gênero? (M/F) [Opcional] - Envie 'pular' para ignorar.");
                cache()->put("chat_{$chatId}_state", 'register_gender');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação do telefone
            if (!empty($text) && !preg_match('/^\d{10,11}$/', $text)) {
                $this->bot->sendMessage($chatId, "Telefone inválido. Por favor, insira um telefone válido com DDD (10 ou 11 dígitos).");
                return;
            }

            $registrationData['phone'] = $text;
            $this->bot->sendMessage($chatId, "Qual o seu gênero? (M/F) [Opcional] - Envie 'pular' para ignorar.");
            cache()->put("chat_{$chatId}_state", 'register_gender');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_gender') {
            if (strtolower($text) === 'pular') {
                $registrationData['gender'] = null;
                $this->bot->sendMessage($chatId, "Qual a sua altura? (em cm) [Opcional] - Envie 'pular' para ignorar.");
                cache()->put("chat_{$chatId}_state", 'register_height');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação do gênero
            if (!empty($text) && !in_array(strtoupper($text), ['M', 'F'])) {
                $this->bot->sendMessage($chatId, "Gênero inválido. Por favor, insira M ou F.");
                return;
            }

            $registrationData['gender'] = strtoupper($text);
            $this->bot->sendMessage($chatId, "Qual a sua altura? (em cm) [Opcional] - Envie 'pular' para ignorar.");
            cache()->put("chat_{$chatId}_state", 'register_height');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_height') {
            if (strtolower($text) === 'pular') {
                $registrationData['height'] = null;
                $this->bot->sendMessage($chatId, "Qual o seu peso? (em kg) [Opcional] - Envie 'pular' para ignorar.");
                cache()->put("chat_{$chatId}_state", 'register_weight');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação da altura
            if (!empty($text) && !preg_match('/^\d+$/', $text)) {
                $this->bot->sendMessage($chatId, "Altura inválida. Por favor, insira um valor válido em cm.");
                return;
            }

            $registrationData['height'] = (int)$text;
            $this->bot->sendMessage($chatId, "Qual o seu peso? (em kg) [Opcional] - Envie 'pular' para ignorar.");
            cache()->put("chat_{$chatId}_state", 'register_weight');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_weight') {
            if (strtolower($text) === 'pular') {
                $registrationData['weight'] = null;
                $this->bot->sendMessage($chatId, "Qual a quantidade de água que você deseja consumir diariamente? (em ml) [Opcional] - Envie 'pular' para ignorar.");
                cache()->put("chat_{$chatId}_state", 'register_notifications');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação do peso
            if (!empty($text) && !preg_match('/^\d+$/', $text)) {
                $this->bot->sendMessage($chatId, "Peso inválido. Por favor, insira um valor válido em kg.");
                return;
            }

            $registrationData['weight'] = (int)$text;
            $this->bot->sendMessage($chatId, "Qual o seu nível de atividade física? [Opcional] - Envie 'pular' para ignorar.\n1. Sedentário\n2. Pouco ativo (1 a 3 vezes na semana)\n3. Ativo (3 a 5 vezes na semana)\n4. Muito ativo (Todos os dias)\n5. Extremamente ativo (Atleta profiissional)");
            cache()->put("chat_{$chatId}_state", 'register_activity_level');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_activity_level') {
            if (strtolower($text) === 'pular') {
                $registrationData['activity_level'] = null;
                $this->bot->sendMessage($chatId, "Qual a quantidade de água que você deseja consumir diariamente? (em ml) [Opcional] - Envie 'pular' para ignorar.");
                cache()->put("chat_{$chatId}_state", 'register_daily_water_amount');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação do nível de atividade física
            if (!empty($text) && !in_array((int)$text, [1, 2, 3, 4, 5])) {
                $this->bot->sendMessage($chatId, "Nível de atividade física inválido.\n1. Sedentário\n2. Pouco ativo (1 a 3 vezes na semana)\n3. Ativo (3 a 5 vezes na semana)\n4. Muito ativo (Todos os dias)\n5. Extremamente ativo (Atleta profiissional)");
                return;
            }
            $niveis = [
                1 => 0.2,
                2 => 0.375,
                3 => 0.55,
                4 => 0.725,
                5 => 0.9
            ];
            $registrationData['activity_level'] = $niveis[(int)$text];
            $this->bot->sendMessage($chatId, "Qual a quantidade de água que você deseja consumir diariamente? (em ml) [Opcional] - Envie 'pular' para ignorar.");
            cache()->put("chat_{$chatId}_state", 'register_daily_water_amount');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_daily_water_amount') {
            if (strtolower($text) === 'pular') {
                $registrationData['daily_water_amount'] = null;
                $this->bot->sendMessage($chatId, "Deseja receber notificações via Telegram? (S/N)");
                cache()->put("chat_{$chatId}_state", 'register_notifications');
                cache()->put("chat_{$chatId}_registration_data", $registrationData);
                return;
            }

            // Validação do consumo diário de água
            if (!empty($text) && !preg_match('/^\d+$/', $text)) {
                $this->bot->sendMessage($chatId, "Quantidade inválida. Por favor, insira um valor válido em ml.");
                return;
            }

            $registrationData['daily_water_amount'] = (int)$text;
            $this->bot->sendMessage($chatId, "Deseja receber notificações via Telegram? (S/N)");
            cache()->put("chat_{$chatId}_state", 'register_notifications');
            cache()->put("chat_{$chatId}_registration_data", $registrationData);
            return;
        }

        if ($state === 'register_notifications') {
            // Validação das notificações
            if (!in_array(strtoupper($text), ['S', 'N'])) {
                $this->bot->sendMessage($chatId, "Opção inválida. Por favor, insira S ou N.");
                return;
            }

            $registrationData['notifications'] = strtoupper($text) === 'S';

            if ($registrationData['notifications']) {
                $registrationData['telegram_user_id'] = $chatId;
                $registrationData['telegram_user_deeplink'] = Uuid::uuid4();
            }
            $this->user = User::create($registrationData);
            cache()->put("user_{$chatId}", $this->user);
            if ($registrationData['notifications']) {
                $subscribeManagement = new NotificationSubscriptionManager();
                $subscribeManagement->subscribe($this->user, 'App\\Notifications\\WaterIntakeReminderDatabase');
                $subscribeManagement->subscribe($this->user, 'App\\Notifications\\WaterIntakeReminderTelegram');
            }

            $this->bot->sendMessage($chatId, "Cadastro concluído com sucesso! Bem-vindo(a), " . $registrationData['name'] . "!");
            $this->sendMainMenu($chatId);
            cache()->put("chat_{$chatId}_state", 'main_menu');
            cache()->forget("chat_{$chatId}_registration_data");
        }
    }

    public function cancelRegistration($chatId)
    {
        $this->bot->sendMessage($chatId, "Cadastro cancelado. Para se cadastrar, envie /menu e selecione a opção Cadastrar-se.");
        cache()->forget("chat_{$chatId}_state");
        cache()->forget("chat_{$chatId}_registration_data");
        return;
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

    protected function validateParam($param, $text)
    {
        if ($param['var_type'] === 'int') {
            return filter_var($text, FILTER_VALIDATE_INT) !== false ? (int) $text : false;
        } elseif ($param['var_type'] === 'string') {
            return is_string($text) ? $text : false;
        } elseif ($param['var_type'] === 'date') {
            $date = \DateTime::createFromFormat('d/m/Y', $text);
            return $date && $date->format('d/m/Y') === $text ? $text : false;
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
                    $this->bot->sendMessage($chatId, $result, 'MarkdownV2');
                } else {
                    $this->bot->sendMessage($chatId, "Ação realizada com sucesso.");
                }
            } else {
                $this->bot->sendMessage($chatId, "Houve um problema ao realizar a ação.");
            }
        } catch (\Exception $e) {
            $this->bot->sendMessage($chatId, "Houve um problema ao realizar a ação. Erro: " . $e->getMessage());
        }
    }
}
