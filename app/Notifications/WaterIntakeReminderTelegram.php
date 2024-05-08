<?php

namespace App\Notifications;

use Asantibanez\LaravelSubscribableNotifications\Contracts\SubscribableNotification;
use Asantibanez\LaravelSubscribableNotifications\Traits\DispatchesToSubscribers;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class WaterIntakeReminderTelegram extends Notification implements SubscribableNotification
{
    use Queueable;
    use DispatchesToSubscribers;
    private $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    public static function subscribableNotificationType(): string
    {
        return 'water-intake-reminder-telegram';
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return 'Lembrete Ingestão de Água via Telegram';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = ['telegram'];

        return $via;
    }

    /**
     * Get the telegram representation of the notification.
     */
    public function toTelegram($notifiable)
    {

        $waterIntakeContainers = $this->user->waterIntakeContainers;

        $ret = TelegramMessage::create()
            ->to($this->user->telegram_user_id)
            ->content("*Hora de beber água!* \n\n" . $this->user->name . " não esqueça de se manter hidratado! \nFaz pelo menos 1 hora que você não bebe água! \n\nEscolha uma das opções abaixo para registrar a ingestão de água:");

        foreach ($waterIntakeContainers as $container) {
            $ret->buttonWithCallback('Bebi 1 ' . $container->name, 'WaterIntake_create_amount:' . $container->size);
        }

        return $ret;
    }
}
