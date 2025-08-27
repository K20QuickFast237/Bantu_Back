<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::updateOrCreate(
            ['name' => 'professionnel'],
            ['description' => 'Utilisateur professionnel']
        );

        Role::updateOrCreate(
            ['name' => 'particulier'],
            ['description' => 'Utilisateur recruteur']
        );
    }
}
