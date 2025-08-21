<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OffreEmploi;
use App\Models\Skill;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OffreSkill>
 */
class OffreSkillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offre_id' => OffreEmploi::inRandomOrder()->first()->id,
            'skill_id' => Skill::inRandomOrder()->first()->id,
            'ordre_aff' => $this->faker->numberBetween(1, 10),
        ];
    }
}
