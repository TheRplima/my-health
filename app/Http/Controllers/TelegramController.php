<?php

namespace App\Http\Controllers;

use App\Http\Resources\TelegramUpdateCollection;
use App\Http\Resources\TelegramUpdateResource;
use App\Models\User;
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
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'user'
                            ],
                            [
                                'var_name' => 'amount',
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
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                            [
                                'var_name' => 'weight',
                                'var_type' => 'int',
                                'required' => true,
                                'question' => 'Qual o seu peso (em kg)?',
                                'error_message' => 'Peso inválido. Qual o seu peso (em kg)?',
                                'get_value_from' => 'response'
                            ],
                            [
                                'var_name' => 'date',
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
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                            [
                                'var_name' => 'name',
                                'var_type' => 'string',
                                'required' => true,
                                'question' => 'Qual o nome do recipiente?',
                                'error_message' => 'Nome inválido. Qual o nome do recipiente?',
                                'get_value_from' => 'response'
                            ],
                            [
                                'var_name' => 'size',
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
                                'var_type' => 'int',
                                'required' => true,
                                'question' => null,
                                'error_message' => null,
                                'get_value_from' => 'system'
                            ],
                            [
                                'var_name' => 'minutes',
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
                if (!$this->user) {
                    $this->bot->sendMessage($chatId, "Olá! Parece que você ainda não está cadastrado em nosso sistema. Por favor, acesse o link abaixo para se cadastrar.");
                    $this->bot->sendMessage($chatId, "https://seu-site.com/cadastro?telegram_user_id={$chatId}");
                    cache()->put('last_update_id', $updateId);
                    cache()->put('telegram_storage_update_ids', $storageUpdateIds);
                    cache()->put('telegram_updates', $storageUpdates);
                    return;
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
        $text = "Menu Principal:\n";
        foreach ($this->modules as $index => $module) {
            $text .= ($index + 1) . ". " . $module['title'] . "\n";
        }
        $text .= "0. Sair";
        $this->bot->sendMessage($chatId, $text);
    }

    protected function sendModuleMenu($chatId, $moduleIndex)
    {
        $module = $this->modules[$moduleIndex];
        $text = "{$module['description']}\n";
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
            cache()->put("chat_{$chatId}_state", 'idle');
            return;
        }

        $index = (int) $text - 1;
        if (isset($this->modules[$index])) {
            $module = $this->modules[$index];
            $text = "{$module['description']}\n";
            foreach ($module['options'] as $key => $option) {
                $text .= "{$key}. {$option['title']}\n";
            }
            $text .= "0. Voltar ao Menu Principal";
            $this->bot->sendMessage($chatId, $text);
            cache()->put("chat_{$chatId}_state", 'module_menu');
            cache()->put("chat_{$chatId}_selected_module", $index);
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
                        $this->bot->sendMessage($chatId, 'Qual o valor de ' . $firstParam['var_name'] . '?');
                    }
                    cache()->put("chat_{$chatId}_state", "handling_option_{$moduleIndex}_{$option}_{$firstParamIndex}");
                } else {
                    $this->bot->sendMessage($chatId, "Deseja fornecer " . $firstParam['var_name'] . "? (sim/não)");
                    cache()->put("chat_{$chatId}_state", "optional_param_{$moduleIndex}_{$option}_{$firstParamIndex}");
                }
            } else {
                $this->executeOptionFunction($chatId, $module['service'], $optionData['function'], $params, $optionData);
                $this->sendModuleMenu($chatId, $moduleIndex);
            }
        } else {
            $this->bot->sendMessage($chatId, "Opção inválida. Por favor, selecione uma opção válida.");
        }
    }

    protected function handleParameterInput($chatId, $text)
    {
        $state = cache()->get("chat_{$chatId}_state");
        preg_match('/handling_option_(\d+)_(\d+)_(\d+)/', $state, $matches);
        preg_match('/optional_param_(\d+)_(\d+)_(\d+)/', $state, $optionalMatches);

        if (!$matches && !$optionalMatches) {
            $this->bot->sendMessage($chatId, "Ocorreu um erro ao processar a sua entrada.");
            return;
        }

        if ($optionalMatches) {
            list(, $moduleIndex, $optionIndex, $paramIndex) = $optionalMatches;
            if (strtolower($text) === 's') {
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
                $this->bot->sendMessage($chatId, "Deseja fornecer " . $nextParam['var_name'] . "? (sim/não)");
                cache()->put("chat_{$chatId}_state", "optional_param_{$moduleIndex}_{$optionIndex}_{$paramIndex}");
            }
        } else {
            $this->executeOptionFunction($chatId, $module['service'], $option['function'], $params, $option);
            cache()->forget("chat_{$chatId}_params");
            cache()->forget("chat_{$chatId}_state");
            $this->sendModuleMenu($chatId, $moduleIndex);
        }
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
