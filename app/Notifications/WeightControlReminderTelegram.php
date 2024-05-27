<?php

namespace App\Notifications;

use Asantibanez\LaravelSubscribableNotifications\Contracts\SubscribableNotification;
use Asantibanez\LaravelSubscribableNotifications\Traits\DispatchesToSubscribers;
use NotificationChannels\Telegram\TelegramMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;

class WeightControlReminderTelegram extends Notification implements SubscribableNotification
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
        return config('weight-control-reminder.subscribable_notification_type.telegram');
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return config('weight-control-reminder.subscribable_notification_type_description.telegram');
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
        $lastWeight = $notifiable->weightControl()->latest()->first();
        $body = "*Hora de se pesar!*";
        $body .= "\n\n" . ucfirst(explode(' ', $notifiable->name)[0]) . ", ";
        if ($lastWeight != null) {
            $body .= "a última vez que se pesou foi *" . Carbon::parse($lastWeight->created_at)->diffForHumans() . "*";
            $body .= " e seu último peso registrado foi *" . $lastWeight->weight . "kg*.\n";
        } else {
            $body .= "você ainda não se pesou.\n";
        }

        $idealWeight = $notifiable->calculateIdealWeight();
        if ($idealWeight != null) {
            $body .= "Seu peso ideal é *" . $idealWeight['ideal'] . "kg*, devendo permanecer entre *" . $idealWeight['min'] . "kg* e *" . $idealWeight['max'] . "kg*.\n";
        }
        $body .= "Lembre-se de se pesar regularmente para acompanhar seu progresso.\n";
        $body .= "\nAqui estão seus últimos pesos:\n";
        $weights = $notifiable->weightControl()->latest()->take(5)->get();
        foreach ($weights as $weight) {
            $body .= Carbon::parse($weight->created_at)->format('d/m/Y') . " - " . $weight->weight . "kg\n";
        }
        $body .= "\n\nMantenha-se motivado e continue com o bom trabalho! 💪";

        return TelegramMessage::create()
            ->to($notifiable->telegram_user_id)
            ->content($body);
    }
}
