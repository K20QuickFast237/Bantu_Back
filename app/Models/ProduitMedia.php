<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitMedia extends Model
{
    use HasFactory;

    protected $table = 'mkt_produit_medias';

    protected $fillable = [
        'produit_id', 'image_link', 'video_link', 'document_link'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
