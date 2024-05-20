<?php

namespace App\Notifications;

use Asantibanez\LaravelSubscribableNotifications\Contracts\SubscribableNotification;
use Asantibanez\LaravelSubscribableNotifications\Traits\DispatchesToSubscribers;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;

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
        return config('water-intake-reminder.subscribable_notification_type.telegram');
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return config('water-intake-reminder.subscribable_notification_type_description.telegram');
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
        $waterIntakeContainers = $notifiable->waterIntakeContainers;
        $waterIntakesToday = $notifiable->waterIntakeToday();
        $lastDrink = $waterIntakesToday->latest()->first();
        $amountIngested = $waterIntakesToday->sum('amount');
        $goal = $notifiable->daily_water_amount;

        if ($lastDrink != null) {
            $body = "*Hora de beber água!*
            \n\n*" . ucfirst(explode(' ', $notifiable->name)[0]) . "* não se esqueça de se manter hidratado, última vez que bebeu água foi às *" . Carbon::parse($lastDrink->created_at)->toTimeString() . "*!
            \n\nVocê ingeriu *" . $amountIngested . "ml* de água hoje, faltam *" . ($goal - $amountIngested) . "ml* para atingir sua meta diária de *" . $goal . "ml*.";
        } else {
            $body = "*Hora de beber água!*
            \n\n*" . ucfirst(explode(' ', $notifiable->name)[0]) . "* não se esqueça de se manter hidratado, você ainda não registrou consumo de água hoje!";
        }

        $buttons = [];
        if (count($waterIntakeContainers) > 0) {
            $body .= "\n\nEscolha uma das opções abaixo para registrar a ingestão de água:";
            foreach ($waterIntakeContainers as $container) {
                $buttons[] = ['Bebi ' . $container->name, 'WaterIntake_create_amount:' . $container->size];
            }
        } else {
            $body .= "\n\nNão há recipientes cadastrados para registrar a ingestão de água. Cadastre um recipiente através do menu enviando /menu e escolhendo a opção *Recipientes*.";
        }

        $ret = TelegramMessage::create()
            ->to($notifiable->telegram_user_id)
            ->content($body);

        if ($buttons && count($buttons) > 0) {
            foreach ($buttons as $button) {
                $ret->buttonWithCallback($button[0], $button[1]);
            }
        }

        return $ret;
    }
}
