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
        Schema::create('mkt_vendeur_mode_paiements', function (Blueprint $table) {
            $table->foreignId('vendeur_id')->constrained('mkt_vendeurs')->cascadeOnDelete();
            $table->foreignId('mode_id')->constrained('mkt_mode_paiements')->cascadeOnDelete();
            $table->primary(['vendeur_id', 'mode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_vendeur_mode_paiements');
    }
};
