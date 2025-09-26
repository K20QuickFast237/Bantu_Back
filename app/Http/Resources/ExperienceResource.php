<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $experience = [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "titre_poste" => $this->titre_poste,
            "nom_entreprise" => $this->nom_entreprise,
            "date_debut" => $this->date_debut,
            "date_fin" => $this->date_fin,
            "description_taches" => $this->description_taches,
            "adresse" => $this->adresse,
            "ville" => $this->ville,
            "pays" => $this->pays,
        ];

        return $experience;
    }
}
