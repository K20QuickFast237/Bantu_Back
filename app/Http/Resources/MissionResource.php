<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MissionResource extends JsonResource
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
            'titre' => $this->titre,
            'description' => $this->description,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'cout' => $this->cout,
            'statut' => $this->statut,
            'freelancer' => $this->whenLoaded('freelancer', function() {
                return [
                    'id' => $this->freelancer->id,
                    'nom_complet' => $this->freelancer->nom_complet,
                    'titre_pro' => $this->freelancer->titre_pro,
                    'user' => $this->freelancer->user ? [
                        'id' => $this->freelancer->user->id,
                        'nom' => $this->freelancer->user->nom,
                        'prenom' => $this->freelancer->user->prenom,
                        'email' => $this->freelancer->user->email,
                    ] : null,
                ];
            }),
            'client' => $this->whenLoaded('client', function() {
                return [
                    'id' => $this->client->id,
                    'nom' => $this->client->nom,
                    'prenom' => $this->client->prenom,
                    'email' => $this->client->email,
                ];
            }),
            'medias' => $this->whenLoaded('medias', function() {
                $medias = $this->medias->map(function($media) {
                    return [
                        'id' => $media->id,
                        'media_type' => $media->media_type,
                        'media_path' => getLinkToFile($media->media_path),
                    ];
                });

                // Classer les médias par type
                return [
                    'images' => $medias->where('media_type', 'image')->values(),
                    'documents' => $medias->where('media_type', 'document')->values(),
                    'videos' => $medias->where('media_type', 'video')->values(),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

