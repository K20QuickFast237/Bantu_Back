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
        Schema::create('mkt_coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendeur_id')->constrained('mkt_vendeurs')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('montant_reduction', 12, 2)->nullable();
            $table->integer('pourcentage_reduction')->nullable();
            $table->date('date_debut')->default(now());
            $table->date('date_expiration')->nullable();
            $table->integer('nbr_depart')->nullable();
            $table->integer('nbr_restant')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_coupons');
    }
};
