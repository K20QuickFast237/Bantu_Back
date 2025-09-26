<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionnelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $professionnel = [
            'id' => $this->id,
            "user_id" => $this->user_id,
            "titre_professionnel" => $this->titre_professionnel,
            "email_pro" => $this->email_pro,
            "telephone_pro" => $this->telephone_pro,
            "nom_entreprise" => $this->nom_entreprise,
            "description_entreprise" => $this->description_entreprise,
            "site_web" => $this->site_web && strpos($this->site_web, 'http') === false ? url($this->site_web) : $this->site_web,
            "logo" => $this->logo && strpos($this->logo, 'http') === false ? url($this->logo) : $this->logo,
            "adresse" => $this->adresse,
            "ville" => $this->ville,
            "pays" => $this->pays,
            "num_contribuable" => $this->num_contribuable,
        ];

        return $professionnel;
    }
}
