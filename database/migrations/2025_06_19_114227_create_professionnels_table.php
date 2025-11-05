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
        Schema::create('professionnels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('titre_professionnel')->nullable();
            $table->string('email_pro')->unique();
            $table->unsignedBigInteger('telephone_pro');
            $table->string('nom_entreprise');
            $table->text('description_entreprise');
            $table->string('site_web')->nullable();
            $table->string('logo')->nullable();
            $table->string('adresse');
            $table->string('ville');
            $table->string('pays');
            $table->string('num_contribuable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionnels');
    }
};
