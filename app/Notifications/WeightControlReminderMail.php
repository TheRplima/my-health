<?php

namespace App\Notifications;

use Asantibanez\LaravelSubscribableNotifications\Contracts\SubscribableNotification;
use Asantibanez\LaravelSubscribableNotifications\Traits\DispatchesToSubscribers;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;

class WeightControlReminderMail extends Notification implements SubscribableNotification
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
        return config('weight-control-reminder.subscribable_notification_type.mail');
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return config('weight-control-reminder.subscribable_notification_type_description.mail');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = ['mail'];

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $lastWeight = $notifiable->weightControl()->latest()->first();
        $body = "<b>" . ucfirst(explode(' ', $notifiable->name)[0]) . "</b>, ";
        if ($lastWeight != null) {
            $body .= "você não se pesa desde <b>" . Carbon::parse($lastWeight->created_at)->diffForHumans() . "</b>";
            $body .= "Seu último peso foi <b>" . $lastWeight->weight . "kg</b>.<br>";
        } else {
            $body .= "você ainda não se pesou.<br>";
        }

        $idealWeight = $notifiable->calculateIdealWeight();
        if ($idealWeight != null) {
            $body .= "Seu peso ideal é <b>" . $idealWeight['ideal'] . "kg</b>, devendo permanecer entre <b>" . $idealWeight['min'] . "kg</b> e <b>" . $idealWeight['max'] . "kg</b>.<br>";
        }
        $body .= "Lembre-se de se pesar regularmente para acompanhar seu progresso.<br>";
        $body .= "<br>Aqui estão seus últimos pesos:<br>";
        $weights = $notifiable->weightControl()->latest()->take(5)->get();
        foreach ($weights as $weight) {
            $body .= "- " . $weight->weight . "kg em " . Carbon::parse($weight->created_at)->format('d/m/Y H:i') . "<br>";
        }
        $body .= "<br><br>Mantenha-se motivado e continue com o bom trabalho! 💪";

        return (new MailMessage)
            ->subject('Hora de se pesar!')
            ->line($body);
    }
}
