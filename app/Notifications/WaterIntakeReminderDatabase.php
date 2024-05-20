<?php

namespace App\Notifications;

use Asantibanez\LaravelSubscribableNotifications\Contracts\SubscribableNotification;
use Asantibanez\LaravelSubscribableNotifications\Traits\DispatchesToSubscribers;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Carbon\Carbon;

class WaterIntakeReminderDatabase extends Notification implements SubscribableNotification
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
        return config('water-intake-reminder.subscribable_notification_type.database');
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return config('water-intake-reminder.subscribable_notification_type_description.database');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = ['database'];

        return $via;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $waterIntakesToday = $notifiable->waterIntakeToday();
        $lastDrink = $waterIntakesToday->latest()->first();
        $amountIngested = $waterIntakesToday->sum('amount');
        $goal = $notifiable->daily_water_amount;

        if ($lastDrink != null) {
            $body = "<b>" . ucfirst(explode(' ', $notifiable->name)[0]) . "</b> não se esqueça de manter-se hidratado, a última vez que bebeu água foi às <b>" . Carbon::parse($lastDrink->created_at)->toTimeString() . "</b>!
            <br />Você ingeriu <b>" . $amountIngested . "ml</b> de água hoje, faltam <b>" . ($goal - $amountIngested) . "ml</b> para atingir sua meta diária de <b>" . $goal . "ml</b>.";
        } else {
            $body = "<b>" . ucfirst(explode(' ', $notifiable->name)[0]) . "</b> não se esqueça de manter-se hidratado, você ainda não registrou consumo de água hoje!";
        }

        return [
            'title' => 'Hora de beber água!',
            'body'  => $body
        ];
    }
}
