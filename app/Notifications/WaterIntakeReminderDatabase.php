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
        $waterIntakesToday = $this->user->waterIntakeToday();
        $lastDrink = $waterIntakesToday->latest()->first();
        $amountIngested = $waterIntakesToday->sum('amount');
        $goal = $this->user->daily_water_amount;

        return [
            'title' => 'Hora de beber água!',
            'body'  => $this->user->name . " não esqueça de se manter hidratado, última vez que bebeu água foi às ." . Carbon::parse($lastDrink->created_at)->toTimeString() . "!
            \n\nVocê ingeriu " . $amountIngested . "ml de água hoje, faltam " . ($goal - $amountIngested) . "ml para atingir sua meta diária de " . $goal . "ml.
            \n\nEscolha uma das opções abaixo para registrar a ingestão de água:",
        ];
    }
}
