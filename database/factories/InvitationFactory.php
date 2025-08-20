<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Candidature;
use App\Models\Professionnel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'candidature_id' => Candidature::inRandomOrder()->first()->id,
            'employeur_id' => Professionnel::inRandomOrder()->first()->id,
            'date_heure_entretien' => $this->faker->dateTimeBetween('now', '+1 month'),
            'type_entretien' => $this->faker->randomElement(['presentiel','telephonique','visio']),
            'lieu' => $this->faker->city,
            'lien_visio' => null,
            'instruction_supl' => $this->faker->sentence,
            'statut' => 'envoyee',
            'date_envoi' => $this->faker->dateTimeThisMonth(),
        ];
    }
}
