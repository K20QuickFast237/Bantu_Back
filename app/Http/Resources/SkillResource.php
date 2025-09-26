<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $skill = [
            'id' => $this->id,
            "nom" => $this->nom,
            "description" => $this->description,
            "icon" => $this->icon,
            "nbr_usage" => $this->nbr_usage,
        ];

        return $skill;
    }
}
