<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryMethodsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('delivery_methods')->insert([
            [
                'name' => 'Livraison standard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Livraison express',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Point relais',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Retrait en boutique',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
