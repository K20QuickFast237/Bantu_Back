<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OffreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $offre = parent::toArray($request);
        if (isset($this->employeur) && $this->employeur) {
            $offre['employeur'] = new ProfessionnelResource($this->employeur);
        }
        if (isset($this->skills)) {
            $offre['skills'] = UserSkillResource::collection($this->skills);
        }
        return $offre;
    }
}
