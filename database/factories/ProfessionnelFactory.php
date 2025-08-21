<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professionnel>
 */
class ProfessionnelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'titre_professionnel' => $this->faker->jobTitle,
            'email_pro' => $this->faker->unique()->companyEmail,
            'telephone_pro' => $this->faker->numerify('##########'),
            'nom_entreprise' => $this->faker->company,
            'description_entreprise' => $this->faker->paragraph,
            'site_web' => $this->faker->url,
            'logo' => $this->faker->imageUrl(),
            'adresse' => $this->faker->streetAddress,
            'ville' => $this->faker->city,
            'pays' => $this->faker->country,
            'num_contribuable' => $this->faker->numerify('##########'),
        ];
    }
}
