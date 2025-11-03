<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            "image_profil"=> getLinkToFile($this->image_profil), // && $this->image_profil ? url(Storage::url($this->image_profil)) : $this->image_profil,
            "cv_link" => getLinkToFile($this->cv_link), //$this->cv_link && strpos($this->cv_link, 'http') === false ? Storage::url($this->cv_link) : $this->cv_link,
            "lettre_motivation_link" => getLinkToFile($this->lettre_motivation_link), //$this->lettre_motivation_link && strpos($this->lettre_motivation_link, 'http') === false ? Storage::url($this->lettre_motivation_link) : $this->lettre_motivation_link,
            "portfolio_link" => getLinkToFile($this->portfolio_link),
            "ressources" => $this->ressources,
            "is_visible" => $this->is_visible ? true : false,
        ];
        
        return $particulier;
    }
}
