<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'] ?? ($this->id ?? null),
            'name' => $this['name'] ?? $this->name,
            'email' => $this['email'] ?? $this->email,
            'password' => $this['password'] ? Hash::make($this['password']) : ($this->password ? Hash::make($this->password) : null),
            'phone' => $this['phone'] ?? ($this->phone ?? null),
            'gender' => $this['gender'] ?? ($this->gender ?? null),
            'dob' => $this['dob'] ?? ($this->dob ?? null),
            'height' => $this['height'] ?? ($this->height ?? null),
            'weight' => $this['weight'] ?? ($this->weight ?? null),
            'daily_water_amount' => $this['daily_water_amount'] ?? ($this->daily_water_amount ?? null),
            'activity_level' => $this['activity_level'] ?? ($this->activity_level ?? null),
            'image' => $this['image'] ?? ($this->image ?? null),
            'telegram_user_id' => $this['telegram_user_id'] ?? ($this->telegram_user_id ?? null),
            'telegram_user_deeplink' => $this['telegram_user_deeplink'] ?? ($this->telegram_user_deeplink ?? null),
        ];
    }
}
