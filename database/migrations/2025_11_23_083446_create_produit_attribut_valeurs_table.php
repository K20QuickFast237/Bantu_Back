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
        Schema::create('mkt_produit_attribut_valeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('mkt_produits', 'id', 'mkt_produit_attribut_valeurs_produit_id_foreign')->cascadeOnDelete();
            $table->foreignId('attribut_valeur_id')->constrained('mkt_attribut_valeurs', 'id', 'mkt_produit_attribut_valeurs_attribut_valeur_id_foreign')->cascadeOnDelete();
            $table->decimal('supplement_cout', 12, 2)->default(0);
            $table->integer('stock_qtte')->default(0);
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_produit_attribut_valeurs');
    }
};
