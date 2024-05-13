<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaterIntakeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => isset($this['id']) ? $this['id'] : (isset($this->id) ? $this->id : null),
            'user_id' => isset($this['user_id']) ? $this['user_id'] : (isset($this->user_id) ? $this->user_id : null),
            'amount' => isset($this['amount']) ? $this['amount'] : (isset($this->amount) ? $this->amount : null),
            'created_at' => isset($this['created_at']) ? $this['created_at'] : (isset($this->created_at) ? $this->created_at : null),
            'updated_at' => isset($this['updated_at']) ? $this['updated_at'] : (isset($this->updated_at) ? $this->updated_at : null),
        ];
    }
}
