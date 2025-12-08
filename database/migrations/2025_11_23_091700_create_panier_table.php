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
        Schema::create('mkt_panier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('mkt_acheteurs')->cascadeOnDelete();
            $table->foreignId('commande_produit_id')->constrained('mkt_commande_produits')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('mkt_produits')->cascadeOnDelete();
            // $table->foreignId('attribut_valeurs')->nullable()->constrained('mkt_attribut_valeurs')->nullOnDelete();
            // $table->json('attribut_valeurs')->nullable(); //->constrained('mkt_attribut_valeurs')->nullOnDelete();
            // $table->integer('quantite')->default(1);
            // $table->decimal('prix_unitaire', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_panier');
    }
};
