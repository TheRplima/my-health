<?php
//create a class that will receive data and send a http post to telegram bot and update home assistant water intake management
namespace App\Hooks;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendCallbackQueryHomeAssistant
{
    public function __construct()
    {
        //
    }

    public function __invoke(array $data)
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_HOOK_RPLIMA_TOKEN') . '/sendMessage';
        $payload = [
            'chat_id' => env('TELEGRAM_BOT_HOOK_RPLIMA_CHATID'),
            'text' => '/drinkWater ' . $data['amount']
        ];

        $response = Http::post($url, $payload);

        if ($response->status() != 200) {
            Log::error('Falha ao enviar mensagem callback para atualizar ingestão de água no Home Assistant. Status code: ' . $response->status() . ' - Response: ' . $response->body());
            return false;
        }

        return true;
    }
}
