<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticulierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $particulier = [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "date_naissance" => $this->date_naissance,
            "telephone" => $this->telephone,
            "adresse" => $this->adresse,
            "ville" => $this->ville,
            "pays" => $this->pays,
            "titre_professionnel" => $this->titre_professionnel,
            "resume_profil" => $this->resume_profil,
            "image_profil"=> $this->image_profil ? url($this->image_profil) : $this->image_profil,
            "cv_link" => $this->cv_link && strpos($this->cv_link, 'http') === false ? url($this->cv_link) : $this->cv_link,
            "lettre_motivation_link" => $this->lettre_motivation_link && strpos($this->lettre_motivation_link, 'http') === false ? url($this->lettre_motivation_link) : $this->lettre_motivation_link,
            "is_visible" => $this->is_visible ? true : false,
        ];
        
        return $particulier;
    }
}
