<?php

namespace App\Services;

use App\Models\User;

class CvSnapshotService
{
    public function generate(User $user): array
    {
        $particulier = $user->particulier;

        return [
            'informations' => [
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'date_naissance' => $particulier->date_naissance,
                'telephone' => $particulier->telephone,
                'adresse' => $particulier->adresse,
                'ville' => $particulier->ville,
                'pays' => $particulier->pays,
                'titre_professionnel' => $particulier->titre_professionnel,
                'resume_profil' => $particulier->resume_profil,
            ],
            'formations' => $user->formations()
                ->select('domaine_etude', 'etablissement', 'date_debut', 'date_fin', 'diplome')
                ->get()
                ->toArray(),
            'experiences' => $user->experiences()
                ->select('titre_poste', 'nom_entreprise', 'date_debut', 'date_fin', 'description_taches', 'adresse', 'ville', 'pays')
                ->get()
                ->toArray(),
            // 'competences' => $user->skills()
            //     ->pluck('nom')
            //     ->toArray(),
        ];
    }
}
