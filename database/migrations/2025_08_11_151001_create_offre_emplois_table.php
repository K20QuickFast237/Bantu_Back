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
        Schema::create('offre_emplois', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employeur_id')->constrained('professionnels')->onDelete('cascade');
            $table->string('titre_poste');
            $table->text('description_poste');
            $table->text('exigences')->nullable();
            $table->text('responsabilites')->nullable();
            $table->string('ville');
            $table->string('pays');
            $table->enum('type_contrat', ['cdi','cdd','interim','stage','alternance','freelance','autre']);
            $table->unsignedInteger('remuneration_min')->nullable();
            $table->unsignedInteger('remuneration_max')->nullable();
            $table->date('date_publication');
            $table->date('date_limite_soumission')->nullable();
            $table->enum('statut', ['active','en_pause','fermee','archivee'])->default('active');
            $table->unsignedInteger('nombre_vues')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_emplois');
    }
};
