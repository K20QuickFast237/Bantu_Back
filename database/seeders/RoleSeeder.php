<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::updateOrCreate(
            ['name' => 'candidat'],
            ['description' => 'Utilisateur candidat']
        );

        Role::updateOrCreate(
            ['name' => 'recruteur'],
            ['description' => 'Utilisateur recruteur']
        );
    }
}
