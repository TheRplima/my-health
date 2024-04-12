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
            'name' => $this['name'],
            'email' => $this['email'],
            'password' => Hash::make($this['password']),
            'phone' => $this['phone'] ?? null,
            'gender' => $this['gender'] ?? null,
            'dob' => $this['dob'] ?? null,
            'height' => $this['height'] ?? null,
            'weight' => $this['weight'] ?? null,
            'daily_water_amount' => $this['daily_water_amount'] ?? null,
            'activity_level' => $this['activity_level'] ?? null
        ];
    }
}
