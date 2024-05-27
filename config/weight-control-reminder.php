<?php

return [
    'enabled' => env('WEIGHT_CONTROL_REMINDER_ENABLED', true),

    'default_notification_setting' => [
        'interval' => env('WEIGHT_CONTROL_REMINDER_INTERVAL', 60),
        'snooze' => env('WEIGHT_CONTROL_REMINDER_SNOOZE', 15),
        'start' => env('WEIGHT_CONTROL_REMINDER_START', '08:00'),
        'end' => env('WEIGHT_CONTROL_REMINDER_END', '23:00'),
    ],

    'subscribable_notification_type' => [
        'mail' => 'weight-conntrol-reminder-mail',
        'database' => 'weight-conntrol-reminder-database',
        'telegram' => 'weight-conntrol-reminder-telegram',
    ],

    'subscribable_notification_type_description' => [
        'mail' => 'Lembrete Registro de Peso via e-mail',
        'database' => 'Lembrete Registro de Peso via notificação interna',
        'telegram' => 'Lembrete Registro de Peso via Telegram',
    ],

    'notification_types' => [
        'weight-conntrol-reminder-mail',
        'weight-conntrol-reminder-database',
        'weight-conntrol-reminder-telegram',
    ],
];
