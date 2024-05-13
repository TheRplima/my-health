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
        $notifiable = $user;
    }

    public static function subscribableNotificationType(): string
    {
        return 'water-intake-reminder-database';
    }

    public static function subscribableNotificationTypeDescription(): string
    {
        return 'Lembrete Ingestão de Água via notificação interna';
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
            $body = "<b>" . $notifiable->name . "</b> não se esqueça de manter-se hidratado, a última vez que bebeu água foi às <b>" . Carbon::parse($lastDrink->created_at)->toTimeString() . "</b>!
            <br />Você ingeriu <b>" . $amountIngested . "ml</b> de água hoje, faltam <b>" . ($goal - $amountIngested) . "ml</b> para atingir sua meta diária de <b>" . $goal . "ml</b>.";
        } else {
            $body = "<b>" . $notifiable->name . "</b> não se esqueça de manter-se hidratado, você ainda não registrou consumo de água hoje!";
        }

        return [
            'title' => 'Hora de beber água!',
            'body'  => $body
        ];
    }
}
