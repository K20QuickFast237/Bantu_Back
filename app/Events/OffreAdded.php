<?php

namespace App\Events;

use App\Models\OffreEmploi;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OffreAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Lorsqu'une offre d'emploi est ajoutée, des profils sont proposés.
     *   L'algorithme de recommandation fonctionne ainsi:
     *       - Les profils ayant aumoins 3/4 des competences de l'offre sont Sélectionnés en les
     *       classant par ordre de nombre de compétences décroissant.
     *       - Pour la suite, une IA devra ensuite trier en fonction des années d'expérience associées
     *       au domaine d'activité de l'offre.
     *       - Attribuer des points aux profils selon ces critères:
     *           - 100% des compétences = 6 oints
     *           - à partir de 85% des compétences = 5 points
     *           - à partir de 70% des compétences = 4 points
     *           - 5 ans d'exp ou plus = 4 points
     *           - 3 ans d'exp ou plus = 3 points
     *           - 2 ans d'exp ou plus = 2 points
     *           - 1 ans d'exp ou plus = 1 point
     * (Plutard pour complexifier les choses on pourra ajouter des points selon le niveau de compétence, la formation, etc.)
     */
    public function __construct(
        public OffreEmploi $offre,
    )
    {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
