<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModePaiement;

class ModePaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            ['nom' => 'Mobile Money'],
            ['nom' => 'Carte bancaire'],
            ['nom' => 'PayPal'],
            ['nom' => 'Virement bancaire'],
            ['nom' => 'Paiement à la livraison']
        ];

        foreach ($options as $opt) {
            ModePaiement::firstOrCreate($opt);
        }
    }
}
