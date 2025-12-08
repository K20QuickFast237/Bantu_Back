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
        Schema::create('mkt_paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('mkt_commandes')->cascadeOnDelete();
            $table->foreignId('acheteur_id')->constrained('mkt_acheteurs')->cascadeOnDelete();
            $table->foreignId('mode_paiement_id')->nullable()->constrained('mkt_mode_paiements');
            $table->foreignId('operateur_id')->nullable()->constrained('mkt_operateur_paiements');

            $table->decimal('montant', 12, 2);
            $table->string('statut');    //->default('en_attente');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_paiements');
    }
};
