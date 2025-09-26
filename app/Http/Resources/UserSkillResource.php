<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "icon" => $this->icon,
            "nbr_usage" => $this->nbr_usage,
            "niveau" => isset($this->pivot) && $this->pivot['niveau'] ? $this->pivot['niveau'] : 'Non défini',
        ];
        // return parent::toArray($request);
        // return $this->map(function ($item) {
            // return [
            //     "id" => $item['id'],
            //     "nom" => $item['nom'],
            //     "description" => $item['description'],
            //     "icon" => $item['icon'],
            //     "nbr_usage" => $item['nbr_usage'],
            //     "niveau" => isset($item['pivot']) && $item['pivot']['niveau'] ? $item['pivot']['niveau'] : 'Non défini',
            // ];
        // })->toArray();
    }
}