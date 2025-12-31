<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class RealisationResource extends JsonResource
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
            'titre' => $this->titre,
            'description' => $this->description,
            'date_realisation' => $this->date_realisation,
            'localisation' => $this->localisation,
            'lien' => $this->lien,
            // 'images' => $this->whenLoaded('images', function() {
            //     return $this->images->map(function($image) {
            //         return [
            //             'id' => $image->id,
            //             'image' => getLinkToFile($image->image),
            //         ];
            //     });
            // }),
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

