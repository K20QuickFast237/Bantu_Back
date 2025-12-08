<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperateurPaiement extends Model
{
    use HasFactory;

    protected $table = 'mkt_operateur_paiements';

    protected $fillable = [
        'nom', 'description', 'isActive', 'available_modes'
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'available_modes' => 'array',
    ];

    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'operateur_id');
    }
}
