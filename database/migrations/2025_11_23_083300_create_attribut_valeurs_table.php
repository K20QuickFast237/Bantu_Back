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
        Schema::create('mkt_attribut_valeurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribut_id')->constrained('mkt_attributs')->cascadeOnDelete();
            $table->string('nom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_attribut_valeurs');
    }
};
