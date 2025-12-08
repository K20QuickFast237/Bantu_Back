<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Particulier;
use App\Models\Professionnel;
use App\Models\Skill;
use App\Models\OffreEmploi;
use App\Models\OffreSkill;
use App\Models\Candidature;
use App\Models\Invitation;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'nom' => 'Test User',
        //     'email' => 'test@example.com',
        
        // ]);

        $this->call(RolesSeeder::class); // ✅ doit être en tout début
        
        $this->call([
            DeliveryMethodsTableSeeder::class,
        ]);


        // 1️⃣ Créer des users
        User::factory(20)->create();

        // 2️⃣ Créer des particuliers et professionnels liés aux users
        Particulier::factory(10)->create();
        Professionnel::factory(5)->create();

        // 3️⃣ Créer des skills
        Skill::factory(15)->create();

        // 4️⃣ Créer des offres d'emploi
        OffreEmploi::factory(10)->create();

        // 5️⃣ Associer des skills aux offres
        foreach (OffreEmploi::all() as $offre) {
            // Chaque offre aura 3 à 5 skills aléatoires
            $skills = Skill::inRandomOrder()->take(rand(3, 5))->pluck('id');
            foreach ($skills as $index => $skill_id) {
                OffreSkill::factory()->create([
                    'offre_id' => $offre->id,
                    'skill_id' => $skill_id,
                    'ordre_aff' => $index + 1,
                ]);
            }
        }

        // 6️⃣ Créer des candidatures
        foreach (Particulier::all() as $particulier) {
            // Chaque particulier postule à 1 à 3 offres aléatoires
            $offres = OffreEmploi::inRandomOrder()->take(rand(1, 3))->pluck('id');
            foreach ($offres as $offre_id) {
                Candidature::factory()->create([
                    'particulier_id' => $particulier->id,
                    'offre_id' => $offre_id,
                ]);
            }
        }

        // 7️⃣ Créer des invitations pour certaines candidatures
        foreach (Candidature::inRandomOrder()->take(5)->get() as $candidature) {
            Invitation::factory()->create([
                'candidature_id' => $candidature->id,
                'employeur_id' => $candidature->offre->employeur_id,
            ]);
        }
    }
}
