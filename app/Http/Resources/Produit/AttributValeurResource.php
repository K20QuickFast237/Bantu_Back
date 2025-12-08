<?php

namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributValeurResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'attribut' => $this->whenLoaded('attribut', function () {
                return [
                    'id' => $this->attribut->id,
                    'nom' => $this->attribut->nom,
                ];
            }),
            // 'created_at' => $this->created_at?->toDateTimeString(),
            // 'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}