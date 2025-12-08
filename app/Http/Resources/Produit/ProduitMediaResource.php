<?php 
namespace App\Http\Resources\Produit;

use Illuminate\Http\Resources\Json\JsonResource;

class ProduitMediaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_link' => $this->image_link ? asset('storage/' . $this->image_link) : null,
            'video_link' => $this->video_link ? asset('storage/' . $this->video_link) : null,
            'document_link' => $this->document_link ? asset('storage/' . $this->document_link) : null,
        ];
    }
}