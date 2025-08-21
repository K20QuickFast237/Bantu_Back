<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Particulier;
use App\Models\OffreEmploi;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidature>
 */
class CandidatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'particulier_id' => Particulier::inRandomOrder()->first()->id,
            'offre_id' => OffreEmploi::inRandomOrder()->first()->id,
            'statut' => $this->faker->randomElement(['en_revision','preselectionne','invitation_entretien','rejete','embauche']),
            'cv_url' => null,
            'motivation_url' => null,
            'commentaire_employeur' => $this->faker->paragraph,
        ];
    }
}
