<?php

namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'valeurs' => $this->whenLoaded('valeurs', function () {
                return $this->valeurs->map(function ($val) {
                    return [
                        'id' => $val->id,
                        'nom' => $val->nom,
                    ];
                })->values();
            }),
            // 'created_at' => $this->created_at?->toDateTimeString(),
            // 'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
