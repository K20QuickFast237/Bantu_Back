<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Professionnel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OffreEmploi>
 */
class OffreEmploiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employeur_id' => Professionnel::inRandomOrder()->first()->id,
            'titre_poste' => $this->faker->jobTitle,
            'description_poste' => $this->faker->paragraph,
            'exigences' => $this->faker->paragraph,
            'responsabilites' => $this->faker->paragraph,
            'ville' => $this->faker->city,
            'pays' => $this->faker->country,
            'type_contrat' => $this->faker->randomElement(['cdi','cdd','interim','stage','alternance','freelance','autre']),
            'remuneration_min' => $this->faker->numberBetween(2000, 4000),
            'remuneration_max' => $this->faker->numberBetween(4001, 8000),
            'date_publication' => $this->faker->date(),
            'date_limite_soumission' => $this->faker->date(),
            'statut' => 'active',
            'nombre_vues' => $this->faker->numberBetween(0, 100),
        ];
    }
}
