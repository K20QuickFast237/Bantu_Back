<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FreelancerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nom_complet' => $this->nom_complet,
            'titre_pro' => $this->titre_pro,
            'description' => $this->description,
            'email_pro' => $this->email_pro,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'photo_profil' => getLinkToFile($this->photo_profil),
            'photo_couverture' => getLinkToFile($this->photo_couverture),
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'nom' => $this->user->nom,
                    'prenom' => $this->user->prenom,
                    'email' => $this->user->email,
                ];
            }),
            'realisations' => RealisationResource::collection($this->whenLoaded('realisations')),
            'notes' => FreelancerNoteResource::collection($this->whenLoaded('notes')),
            'missions_count' => $this->whenLoaded('missions', fn() => $this->missions->count()),
            'average_score' => $this->whenLoaded('notes', function() {
                if ($this->notes->count() > 0) {
                    return round($this->notes->avg('score'), 2);
                }
                return null;
            }),
        ];
    }
}

