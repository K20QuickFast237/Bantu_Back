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
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('particulier_id')->constrained('particuliers')->onDelete('cascade');
            $table->foreignId('offre_id')->constrained('offre_emplois')->onDelete('cascade');
            $table->enum('statut', ['en_revision','preselectionne','invitation_entretien','rejete','embauche'])->default('en_revision');
            $table->string('cv_url')->nullable();
            $table->string('motivation_url')->nullable();
            $table->text('commentaire_employeur')->nullable();
            $table->timestamps();
            
            $table->unique(['particulier_id', 'offre_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
