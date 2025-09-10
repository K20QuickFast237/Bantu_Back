<?php

namespace App\Listeners;

use App\Events\OffreAdded;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecommendProfils
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public User $user
    )
    {}

    /**
     * Lorsqu'une offre d'emploi est ajoutée, des profils sont proposés.
     * L'algorithme de recommandation fonctionne ainsi:
     *     - Les profils ayant aumoins 3/4 des competences de l'offre sont Sélectionnés en les
     *     classant par ordre de nombre de compétences décroissant.
     *     - Pour la suite, une IA devra ensuite trier en fonction des années d'expérience associées
     *     au domaine d'activité de l'offre.
     *     - Attribuer des points aux profils selon ces critères:
     *         - 100% des compétences = 6 oints
     *         - à partir de 85% des compétences = 5 points
     *         - à partir de 70% des compétences = 4 points
     *         - 5 ans d'exp ou plus = 4 points
     *         - 3 ans d'exp ou plus = 3 points
     *         - 2 ans d'exp ou plus = 2 points
     *         - 1 ans d'exp ou plus = 1 point
     */
    public function handle(OffreAdded $event): void
    {
        // step1: récupérer les compétences de l'offre et les dénombrer
        // step2: récupérer les profils ayant au moins 3/4 des competences de l'offre
        // step3: attribuer des points aux profils

        $pertinence_factor = 3/4;
        // step1
        $skills = $event->offre->skills()->get();
        $skills_count = $skills->count();

        // step2
        $profils = $this->user->whereHas('skills', function($q) use ($skills, $skills_count, $pertinence_factor) {
            $q->where('skill_id', 'in', $skills->pluck('id')->toArray())
                ->where('pivot.ordre_aff', '>=', ceil($skills_count * $pertinence_factor));
        });

        print_r($profils);
        // step3
    }
}
