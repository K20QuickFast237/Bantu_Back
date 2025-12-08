<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategorieProduit;

class CategorieProduitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nom' => 'Mode & Vêtements'],
            ['nom' => 'Électronique'],
            ['nom' => 'Maison & Décoration'],
            ['nom' => 'Beauté & Santé'],
            ['nom' => 'Sport & Loisirs']
        ];

        foreach ($categories as $cat) {
            CategorieProduit::firstOrCreate($cat);
        }
    }
}
