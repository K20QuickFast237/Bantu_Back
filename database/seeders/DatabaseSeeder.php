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
        $this->call([
            // RolePermissionSeeder::class,
            AdminSeeder::class,
            CategorieProduitSeeder::class,
            OptionLivraisonSeeder::class,
            PaiementSeeder::class,
            VendeurSeeder::class,
            AcheteurSeeder::class,
        ]);
    }
}
