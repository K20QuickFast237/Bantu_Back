<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class userSkill extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return $this->map(function ($item) {
            return [
                "id" => $item['id'],
                "nom" => $item['nom'],
                "description" => $item['description'],
                "icon" => $item['icon'],
                "nbr_usage" => $item['nbr_usage'],
                "niveau" => isset($item['pivot']) && $item['pivot']['niveau'] ? $item['pivot']['niveau'] : 'Non défini',
            ];
        })->toArray();
    }
}