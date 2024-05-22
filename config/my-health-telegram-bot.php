<?php

$modversion = array(
    'name' => 'Minha Saúde',
    'version' => '1.0.0',
    'description' => "Minha saúde é um sistema de gerenciamento de saúde pessoal.\nCom o Minha Saúde, você pode acompanhar sua ingestão de água, controlar seu peso, registrar atividades físicas e monitorar sua saúde em geral.",
    'author' => [
        'name' => "Rodrigo Lima",
        'email' => 'rplima.dev@gmail.com',
        'website' => null,
    ],
    'credits' => "Rodrigo Lima"
);

$modversion['modules'] = array(
    [
        'enabled' => true,
        'title' => 'Perfi de Usuário',
        'description' => 'Gerencie seu perfil de usuário.',
        'service' => 'User',
        'options' => [
            1 => [
                'title' => 'Ver dados perfil',
                'function' => 'showProfileData',
                'return_type' => 'result',
                'return_message' => null,
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
                'title' => 'Alterar Nome',
                'function' => 'updateProfileName',
                'return_type' => 'message',
                'return_message' => 'Nome alterado com sucesso.',
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
                        'var_caption' => 'Nome',
                        'var_type' => 'full_name',
                        'required' => true,
                        'question' => 'Qual o seu nome?',
                        'error_message' => 'Nome inválido. Qual o seu nome?',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            3 => [
                'title' => 'Alterar Senha',
                'function' => 'updateProfilePassword',
                'return_type' => 'message',
                'return_message' => 'Senha alterada com sucesso.',
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
                        'var_name' => 'old_password',
                        'var_caption' => 'Senha antiga',
                        'var_type' => 'password',
                        'required' => true,
                        'question' => 'Qual a sua senha antiga?',
                        'error_message' => 'Senha inválida. Qual a sua senha antiga?',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'new_password',
                        'var_caption' => 'Nova senha',
                        'var_type' => 'password',
                        'required' => true,
                        'question' => 'Qual a sua nova senha?',
                        'error_message' => 'Senha inválida. Qual a sua nova senha?',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'confirm_password',
                        'var_caption' => 'Confirmação da nova senha',
                        'var_type' => 'password',
                        'required' => true,
                        'question' => 'Confirme a sua nova senha.',
                        'error_message' => 'Senha inválida. Confirme a sua nova senha.',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            4 => [
                'title' => 'Alterar Telefone',
                'function' => 'updateProfilePhone',
                'return_type' => 'message',
                'return_message' => 'Telefone alterado com sucesso.',
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
                        'var_name' => 'phone',
                        'var_caption' => 'Telefone',
                        'var_type' => 'phone',
                        'required' => true,
                        'question' => 'Qual o seu telefone? (DDD + Número) Ex: 11999999999',
                        'error_message' => 'Telefone inválido. Qual o seu telefone? (DDD + Número) Ex: 11999999999',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            5 => [
                'title' => 'Alterar Gênero',
                'function' => 'updateProfileGender',
                'return_type' => 'message',
                'return_message' => 'Gênero alterado com sucesso.',
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
                        'var_name' => 'gender',
                        'var_caption' => 'Gênero',
                        'var_type' => 'gender',
                        'required' => true,
                        'question' => 'Qual o seu gênero? (M/F)',
                        'error_message' => 'Gênero inválido. Qual o seu gênero? (M/F)',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            6 => [
                'title' => 'Alterar Data de Nascimento',
                'function' => 'updateProfileBirthdate',
                'return_type' => 'message',
                'return_message' => 'Data de nascimento alterada com sucesso.',
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
                        'var_name' => 'dob',
                        'var_caption' => 'Data de Nascimento',
                        'var_type' => 'birthdate',
                        'required' => true,
                        'question' => 'Qual a sua data de nascimento? (DD/MM/YYYY)',
                        'error_message' => 'Data inválida. Qual a sua data de nascimento? (DD/MM/YYYY)',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            7 => [
                'title' => 'Alterar Altura',
                'function' => 'updateProfileHeight',
                'return_type' => 'message',
                'return_message' => 'Altura alterada com sucesso.',
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
                        'var_name' => 'height',
                        'var_caption' => 'Altura',
                        'var_type' => 'int',
                        'required' => true,
                        'question' => 'Qual a sua altura? (em cm)',
                        'error_message' => 'Altura inválida. Qual a sua altura? (em cm)',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            8 => [
                'title' => 'Alterar Peso',
                'function' => 'updateProfileWeight',
                'return_type' => 'message',
                'return_message' => 'Peso alterado com sucesso.',
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
                        'question' => 'Qual o seu peso? (em kg)',
                        'error_message' => 'Peso inválido. Qual o seu peso? (em kg)',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            9 => [
                'title' => 'Alterar Objetivo de Água Diário',
                'function' => 'updateProfileDailyWaterAmount',
                'return_type' => 'message',
                'return_message' => 'Objetivo de água diário alterado com sucesso.',
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
                        'var_name' => 'daily_water_amount',
                        'var_caption' => 'Objetivo de Água Diário',
                        'var_type' => 'int',
                        'required' => true,
                        'question' => 'Qual a quantidade de água que você deseja consumir diariamente? (em ml)',
                        'error_message' => 'Valor inválido. Qual a quantidade de água que você deseja consumir diariamente? (em ml)',
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            10 => [
                'title' => 'Alterar Nível de Atividade Física',
                'function' => 'updateProfilePhysicalActivityLevel',
                'return_type' => 'message',
                'return_message' => 'Nível de atividade física alterado com sucesso.',
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
                        'var_name' => 'activity_level',
                        'var_caption' => 'Nível de Atividade Física',
                        'var_type' => 'activity_level',
                        'required' => true,
                        'question' => "Qual o seu nível de atividade física?\n1. Sedentário\n2. Pouco ativo (1 a 3 vezes na semana)\n3. Ativo (3 a 5 vezes na semana)\n4. Muito ativo (Todos os dias)\n5. Extremamente ativo (Atleta profiissional)",
                        'error_message' => "Valor inválido. Qual o seu nível de atividade física?\n1. Sedentário\n2. Pouco ativo (1 a 3 vezes na semana)\n3. Ativo (3 a 5 vezes na semana)\n4. Muito ativo (Todos os dias)\n5. Extremamente ativo (Atleta profiissional)",
                        'get_value_from' => 'response'
                    ]
                ],
            ],
            11 => [
                'title' => 'Alterar foto de perfil',
                'function' => 'updateProfilePhoto',
                'return_type' => 'message',
                'return_message' => 'Foto de perfil alterada com sucesso.',
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
                        'var_name' => 'photo',
                        'var_caption' => 'Foto de Perfil',
                        'var_type' => 'photo',
                        'required' => true,
                        'question' => 'Envie a foto que deseja usar como foto de perfil.',
                        'error_message' => 'Foto inválida. Envie a foto que deseja usar como foto de perfil.',
                        'get_value_from' => 'response'
                    ]
                ],
            ]
        ]
    ],
    [
        'enabled' => true,
        'title' => 'Ingestão de água',
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
            3 => [
                'title' => 'Cadastrar recipiente',
                'function' => 'createWaterIntakeContainer',
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
            4 => [
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
        'enabled' => true,
        'title' => 'Peso',
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
        'enabled' => true,
        'title' => 'Atividades Físicas',
        'description' => 'Registre suas atividades físicas diárias e acompanhe sua evolução.',
        'service' => 'PhysicalActivity',
        'options' => [
            1 => [
                'title' => 'Registrar atividade física',
                'function' => 'createFromBot',
                'return_type' => 'result',
                'return_message' => null,
                'params' => [
                    [
                        'var_name' => 'user_id',
                        'var_type' => 'int',
                        'var_caption' => 'Usuário',
                        'required' => true,
                        'question' => null,
                        'error_message' => null,
                        'get_value_from' => 'system'
                    ],
                    [
                        'var_name' => 'name',
                        'var_type' => 'string',
                        'var_caption' => 'Nome da atividade',
                        'required' => true,
                        'question' => 'Qual o nome da atividade?',
                        'error_message' => 'Nome inválido. Qual o nome da atividade?',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'description',
                        'var_type' => 'string',
                        'var_caption' => 'Descrição da atividade',
                        'required' => false,
                        'question' => 'Descreva a atividade realizada.',
                        'error_message' => 'Descrição inválida. Descreva a atividade realizada.',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'category_id',
                        'var_type' => 'physical_activity_category',
                        'var_caption' => 'Categoria',
                        'required' => true,
                        'question' => 'function showCategoryOptions()',
                        'error_message' => 'function showCategoryOptions(true)',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'sport_id',
                        'var_type' => 'physical_activity_sport',
                        'var_caption' => 'Atividade',
                        'required' => true,
                        'question' => 'function showSportOptions(category_id)',
                        'error_message' => 'function showSportOptions(category_id,true)',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'date',
                        'var_type' => 'date',
                        'var_caption' => 'Data',
                        'required' => true,
                        'question' => 'Qual a data da atividade (DD/MM/YYYY)?',
                        'error_message' => 'Data inválida. Qual a data da atividade (DD/MM/YYYY)?',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'start_time',
                        'var_type' => 'time',
                        'var_caption' => 'Hora de início',
                        'required' => true,
                        'question' => 'Que horas iniciou a atividade (h:m)?',
                        'error_message' => 'Hora de início inválida. Que horas iniciou a atividade (h:m)?',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'end_time',
                        'var_type' => 'time',
                        'var_caption' => 'Hora de fim',
                        'required' => true,
                        'question' => 'Que horas terminou a atividade (h:m)?',
                        'error_message' => 'Hora de fim inválida. Que horas terminou a atividade (h:m)?',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'effort_level',
                        'var_type' => 'effort',
                        'var_caption' => 'Nível de esforço',
                        'required' => true,
                        'question' => 'function showEffortLevelsOptions()',
                        'error_message' => 'function showEffortLevelsOptions(true)',
                        'get_value_from' => 'response'
                    ],
                    [
                        'var_name' => 'observations',
                        'var_type' => 'string',
                        'var_caption' => 'Observações',
                        'required' => false,
                        'question' => 'Alguma observação sobre a atividade?',
                        'error_message' => 'Observação inválida. Alguma observação sobre a atividade?',
                        'get_value_from' => 'response'
                    ],
                ]
            ],
            2 => [
                'title' => 'Resumo semanal de atividades físicas',
                'function' => 'showWeeklySummary',
                'return_type' => 'result',
                'return_message' => null,
                'params' => [
                    [
                        'var_name' => 'user_id',
                        'var_type' => 'int',
                        'var_caption' => 'Usuário',
                        'required' => true,
                        'question' => null,
                        'error_message' => null,
                        'get_value_from' => 'system'
                    ],
                ]
            ],
            3 => [
                'title' => 'Resumo mensal de atividades físicas',
                'function' => 'showMonthlySummary',
                'return_type' => 'result',
                'return_message' => null,
                'params' => [
                    [
                        'var_name' => 'user_id',
                        'var_type' => 'int',
                        'var_caption' => 'Usuário',
                        'required' => true,
                        'question' => null,
                        'error_message' => null,
                        'get_value_from' => 'system'
                    ],
                ]
            ]
        ]
    ],
    [
        'enabled' => true,
        'title' => 'Configurações',
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
    ]
);

return $modversion;
