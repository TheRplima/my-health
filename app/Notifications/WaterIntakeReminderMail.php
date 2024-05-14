<?php

namespace App\Notifications;

use Asantibanez\LaravelSubscribableNotifications\Contracts\SubscribableNotification;
use Asantibanez\LaravelSubscribableNotifications\Traits\DispatchesToSubscribers;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;

class WaterIntakeReminderMail extends Notification implements SubscribableNotification
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
        return config('water-intake-reminder.subscribable_notification_type.mail');
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return config('water-intake-reminder.subscribable_notification_type_description.mail');
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
        $waterIntakesToday = $notifiable->waterIntakeToday();
        $lastDrink = $waterIntakesToday->latest()->first();
        $amountIngested = $waterIntakesToday->sum('amount');
        $goal = $notifiable->daily_water_amount;

        if ($lastDrink != null) {
            $body = "<b>" . $notifiable->name . "</b> não se esqueça de manter-se hidratado, a última vez que bebeu água foi às <b>" . Carbon::parse($lastDrink->created_at)->toTimeString() . "</b>!
            <br />Você ingeriu <b>" . $amountIngested . "ml</b> de água hoje, faltam <b>" . ($goal - $amountIngested) . "ml</b> para atingir sua meta diária de <b>" . $goal . "ml</b>.";
        } else {
            $body = "<b>" . $notifiable->name . "</b> não se esqueça de manter-se hidratado, você ainda não registrou consumo de água hoje!";
        }

        return (new MailMessage)
            ->subject('Hora de beber água!')
            ->line($body);
    }
}
