<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class WaterIntakeReminder extends Notification
{
    use Queueable;
    private $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = ['database'];
        if ($this->user->telegram_user_id !== null) {
            $via[] = 'telegram';
        }

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Hora de beber água!')
            ->line($this->user->name . ', faz pelo menos uma hora que você não bebe água! Não se esqueça de se manter hidratado!');
        // ->action('Notification Action', url('/'))
        // ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Hora de beber água!',
            'body'  => $this->user->name . ', faz pelo menos uma hora que você não bebe água! Não se esqueça de se manter hidratado!',
        ];
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
