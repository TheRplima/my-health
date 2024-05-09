<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelegramUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = isset($this['callback_query']) ? 'callback_query' : 'message';
        $command = $type === 'callback_query' ? $this['callback_query']['data'] : $this['message']['text'];

        return [
            'update_id' => $this['update_id'],
            'date' => $type === 'callback_query' ? $this['callback_query']['message']['date'] : $this['message']['date'],
            'type' => $type,
            'chat_id' => $type === 'callback_query' ? $this['callback_query']['message']['chat']['id'] : $this['message']['chat']['id'],
            'command' => $this->extractCommand($command, $type),
        ];
    }

    public function extractCommand(string $command, $type): array
    {
        if ($type === 'callback_query') {
            return [
                'service' => explode('_', $command)[0],
                'function' => explode('_', $command)[1],
                'field' => explode(':', explode('_', $command)[2])[0],
                'value' => explode(':', explode('_', $command)[2])[1],
            ];
        }
        if ($type === 'message') {
            return [
                'service' => substr(explode(' ', $command)[0], 1),
                'function' => null,
                'field' => null,
                'value' => explode(' ', $command)[1] ?? 0,
            ];
        }

        return [
            'service' => $command,
            'function' => null,
            'field' => null,
            'value' => null,
        ];
    }
}
