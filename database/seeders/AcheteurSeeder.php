<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Acheteur;

class AcheteurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'acheteur@market.com'],
            [
                'nom' => 'Acheteur Démo',
                'password' => bcrypt('password'),
            ]
        );

        // $user->assignRole('acheteur');

        Acheteur::firstOrCreate(['user_id' => $user->id]);
    }
}
