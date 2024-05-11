<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $receivedTime = now()->diffInMinutes($this['created_at']) < 120 ? Carbon::parse($this['created_at'])->toTimeString() : Carbon::parse($this['created_at'])->diffForHumans();
        return [
            'id' => $this['id'] ?? null,
            'image' => asset('storage/images/notifications/water-intake-reminder-icon.png'),
            'message' => $this['data']['title'],
            'receivedTime' => $receivedTime
        ];
    }
}
