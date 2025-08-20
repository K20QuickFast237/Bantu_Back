<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Particulier>
 */
class ParticulierFactory extends Factory
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
            'date_naissance' => $this->faker->date(),
            'telephone' => $this->faker->numerify('##########'),
            'adresse' => $this->faker->streetAddress,
            'ville' => $this->faker->city,
            'pays' => $this->faker->country,
            'titre_professionnel' => $this->faker->jobTitle,
            'resume_profil' => $this->faker->paragraph,
            'image_profil' => $this->faker->imageUrl(),
            'cv_link' => null,
            'lettre_motivation_link' => null,
            'is_visible' => true,
        ];
    }
}
