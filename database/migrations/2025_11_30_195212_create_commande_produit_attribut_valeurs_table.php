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
        Schema::create('mkt_commande_produit_attribut_valeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_produit_id')->constrained('mkt_commande_produits', 'id', 'mkt_cmd_prod_att_val_commande_produit_id_foreign')->cascadeOnDelete();
            $table->foreignId('produit_attribut_valeur_id')->constrained('mkt_produit_attribut_valeurs', 'id', 'mkt_cmd_prod_att_val_produit_attribut_valeur_id_foreign')->cascadeOnDelete();
            
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_commande_produit_attribut_valeurs');
    }
};
