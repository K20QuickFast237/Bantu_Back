<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerNoteResource extends JsonResource
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
            'freelancer_id' => $this->freelancer_id,
            'client_id' => $this->client_id,
            'mission_id' => $this->mission_id,
            'score' => $this->score,
            'description' => $this->description,
            'freelancer' => $this->whenLoaded('freelancer', function() {
                return [
                    'id' => $this->freelancer->id,
                    'nom_complet' => $this->freelancer->nom_complet,
                ];
            }),
            'client' => $this->whenLoaded('client', function() {
                return [
                    'id' => $this->client->id,
                    'nom' => $this->client->nom,
                    'prenom' => $this->client->prenom,
                ];
            }),
            'mission' => $this->whenLoaded('mission', function() {
                return [
                    'id' => $this->mission->id,
                    'titre' => $this->mission->titre,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

