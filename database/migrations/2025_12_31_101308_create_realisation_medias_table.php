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
        Schema::create('realisation_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realisation_id')->constrained('realisations')->onDelete('cascade');
            $table->string('media_type');
            $table->string('media_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisation_medias');
    }
};
