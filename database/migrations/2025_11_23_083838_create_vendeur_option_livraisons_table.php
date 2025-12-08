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
        Schema::create('mkt_vendeur_option_livraisons', function (Blueprint $table) {
            $table->foreignId('vendeur_id')->constrained('mkt_vendeurs')->cascadeOnDelete();
            $table->foreignId('option_id')->constrained('mkt_option_livraisons')->cascadeOnDelete();
            $table->primary(['vendeur_id', 'option_id']);
            $table->decimal('prix', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_vendeur_option_livraisons');
    }
};
