<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = []; // aucun champ à remplir directement

    /**
     * Les participants de la conversation (relation many-to-many)
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participant')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Les messages de la conversation
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Récupérer l'autre participant d'une conversation privée
     */
    public function otherParticipant($userId)
    {
        return $this->participants()->where('users.id', '!=', $userId)->first();
    }
}
