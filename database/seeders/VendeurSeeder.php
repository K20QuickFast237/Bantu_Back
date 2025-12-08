<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendeur;

class VendeurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'vendeur@market.com'],
            [
                'nom' => 'Vendeur Démo',
                'password' => bcrypt('password'),
            ]
        );

        // $user->assignRole('vendeur');

        Vendeur::firstOrCreate([
            'user_id' => $user->id,
            'nom' => 'Boutique Démo',
            'email' => 'Boutiquedemo@vendeur.com',
            'description' => 'Boutique de démonstration Marketplace.'
        ]);
    }
}
