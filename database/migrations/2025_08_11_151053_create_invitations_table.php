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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained('candidatures')->onDelete('cascade');
            $table->foreignId('employeur_id')->constrained('professionnels')->onDelete('cascade');
            $table->dateTime('date_heure_entretien');
            $table->enum('type_entretien', ['presentiel','telephonique','visio']);
            $table->string('lieu')->nullable();
            $table->string('lien_visio')->nullable();
            $table->text('instruction_supl')->nullable();
            $table->enum('statut', ['envoyee','acceptee','refusee','annulee'])->default('envoyee');
            $table->dateTime('date_envoi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
