<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

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
            'data' => $this['callback_query']['data'] ?? '',
        ];
    }

    public function extractCommand(string $command, $type): array
    {
        if ($type === 'callback_query') {
            $commands = explode('|', $command)[0];
            $items = explode(',', explode('|', $command)[1]);
            $fields = $values = [];
            foreach ($items as $item) {
                $field = explode(':', $item)[0];
                $value = isset(explode(':', $item)[1]) ? explode(':', $item)[1] : null;
                $fields[] = $field;
                $values[] = $value;
            }
            if (count($fields) === 1) {
                $fields = $fields[0];
            }
            if (count($values) === 1) {
                $values = $values[0];
            }
            return [
                'from' => explode('_', $commands)[0],
                'service' => explode('_', $commands)[1],
                'function' => explode('_', $commands)[2],
                'field' => $fields,
                'value' => $values,
            ];
        }
        if ($type === 'message') {
            return [
                'service' => strpos($command, '/') === 0 ? substr($command, 1) : $command,
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
