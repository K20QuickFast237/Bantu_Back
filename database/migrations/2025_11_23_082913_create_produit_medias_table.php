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
        Schema::create('mkt_produit_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('mkt_produits')->cascadeOnDelete();
            $table->string('image_link')->nullable();
            $table->string('video_link')->nullable();
            $table->string('document_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_produit_medias');
    }
};
