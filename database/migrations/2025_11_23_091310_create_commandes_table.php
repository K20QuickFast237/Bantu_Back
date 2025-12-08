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
        Schema::create('mkt_commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acheteur_id')->constrained('mkt_acheteurs')->cascadeOnDelete();
            $table->foreignId('option_livraison_id')->nullable()->constrained('mkt_option_livraisons');
            $table->foreignId('mode_paiement_id')->nullable()->constrained('mkt_mode_paiements');
            $table->foreignId('operateur_id')->nullable()->constrained('mkt_operateur_paiements');


            $table->string('statut')->default('en_attente');
            $table->string('adresse_livraison')->nullable();
            $table->string('ville_livraison')->nullable();
            $table->string('pays_livraison')->nullable();

            $table->decimal('sous_total', 12, 2)->default(0);
            $table->decimal('frais_livraison', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('mkt_coupons')->noActionOnDelete();
            $table->decimal('reduction', 12, 2)->default(0);
            $table->decimal('net_a_payer', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_commandes');
    }
};
