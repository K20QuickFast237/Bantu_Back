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
        Schema::create('mkt_commande_produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('mkt_commandes')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('mkt_produits')->cascadeOnDelete();
            // $table->foreignId('attribut_valeur_id')->nullable()->constrained('mkt_attribut_valeurs')->nullOnDelete();
            // $table->json('attribut_valeurs')->nullable(); //->constrained('mkt_attribut_valeurs')->nullOnDelete();

            $table->integer('quantite');
            $table->decimal('prix_unitaire', 12, 2);
            $table->decimal('prix_total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_commande_produits');
    }
};
