<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mkt_favoris', function (Blueprint $table) {
            $table->foreignId('acheteur_id')->constrained('mkt_acheteurs')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('mkt_produits')->cascadeOnDelete();
            $table->primary(['acheteur_id', 'produit_id']);
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_favoris');
    }
};
