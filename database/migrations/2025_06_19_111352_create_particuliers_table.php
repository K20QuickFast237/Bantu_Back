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
        Schema::create('particuliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_naissance')->nullable();
            $table->unsignedBigInteger('telephone');
            $table->string('adresse');
            $table->string('ville');
            $table->string('pays');
            $table->string('titre_professionnel');
            $table->text('resume_profil')->nullable();
            $table->string('image_profil')->nullable();
            $table->string('cv_link')->nullable();
            $table->string('lettre_motivation_link')->nullable();
            $table->string('portfolio_link')->nullable();
            $table->string('ressources_link')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('particuliers');
    }
};
