<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TelegramUpdateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }

    public function getUpdateIds(): array
    {
        return $this->collection->map(function ($item) {
            return $item['update_id'];
        })->toArray();
    }

    public function removeItem($updateId): self
    {
        return new self($this->collection->filter(function ($item) use ($updateId) {
            return $item['update_id'] !== $updateId;
        }));
    }

    public function getItemsByType($type): self
    {
        return new self($this->collection->filter(function ($item) use ($type) {
            $item = $item->toArray(request());
            return $item['type'] === $type;
        }));
    }
}
