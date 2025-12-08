<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModePaiement;
use App\Models\OperateurPaiement;

class PaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Modes
        $modes = [
            ['nom' => 'Mobile Money'],
            ['nom' => 'Carte bancaire'],
            ['nom' => 'Paiement à la livraison'],
        ];

        foreach ($modes as $m) {
            ModePaiement::firstOrCreate($m);
        }

        // Opérateurs Mobile Money
        $operateurs = [
            [
                'nom' => 'Orange Money',
                'isActive' => true,
                'available_modes' => json_encode(['mobile_money'])
            ],
            [
                'nom' => 'MTN Mobile Money',
                'isActive' => true,
                'available_modes' => json_encode(['mobile_money'])
            ],
        ];

        foreach ($operateurs as $op) {
            OperateurPaiement::firstOrCreate(['nom' => $op['nom']], $op);
        }
    }
}
