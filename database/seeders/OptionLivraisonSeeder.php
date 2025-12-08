<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OptionLivraison;

class OptionLivraisonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            ['nom' => 'Expédition standard - 3 à 5 jours', 'isActive' => true],
            ['nom' => 'Expéditione express - 24h', 'isActive' => true],
            ['nom' => 'Livraison à domicile', 'isActive' => true],
            ['nom' => 'Retrait en boutique', 'isActive' => true],
            ['nom' => 'Retrait en point relais', 'isActive' => true],
        ];

        foreach ($options as $opt) {
            OptionLivraison::firstOrCreate($opt);
        }
    }
}
