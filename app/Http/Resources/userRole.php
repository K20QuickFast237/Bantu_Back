<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class userRole extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->map(function ($item) {
            return [
                "id" => $item['id'],
                "name" => $item['name'],
                "isCurrent" => isset($item['pivot']) && $item['pivot']['isCurrent'] ? true : false,
            ];
        })->toArray();
    }
}
