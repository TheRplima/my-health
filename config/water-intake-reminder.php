<?php

return [
    'enabled' => env('WATER_INTAKE_REMINDER_ENABLED', true),

    'default_notification_setting' => [
        'interval' => env('WATER_INTAKE_REMINDER_INTERVAL', 60),
        'snooze' => env('WATER_INTAKE_REMINDER_SNOOZE', 15),
        'start' => env('WATER_INTAKE_REMINDER_START', '08:00'),
        'end' => env('WATER_INTAKE_REMINDER_END', '23:00'),
    ],

    'subscribable_notification_type' => [
        'mail' => 'water-intake-reminder-mail',
        'database' => 'water-intake-reminder-database',
        'telegram' => 'water-intake-reminder-telegram',
    ],

    'subscribable_notification_type_description' => [
        'mail' => 'Lembrete Ingestão de Água via e-mail',
        'database' => 'Lembrete Ingestão de Água via notificação interna',
        'telegram' => 'Lembrete Ingestão de Água via Telegram',
    ],

    'notification_types' => [
        'water-intake-reminder-mail',
        'water-intake-reminder-database',
        'water-intake-reminder-telegram',
    ],
];
