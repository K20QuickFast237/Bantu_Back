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
        Schema::table('offre_emplois', function (Blueprint $table) {

            // Étape 2 - Compléments sur l'offre
            $table->string('fonction')->nullable()->after('titre_poste'); 
            $table->string('lieu_travail')->nullable()->after('fonction');
            $table->string('document_annonce')->nullable()->after('remuneration_max');

            // Étape 3 - Modalités de candidature
            $table->string('email_candidature')->nullable()->after('date_limite_soumission');
            $table->string('url_candidature')->nullable()->after('email_candidature');
            $table->text('instructions_candidature')->nullable()->after('url_candidature');
            $table->json('documents_requis')->nullable()->after('instructions_candidature');
            $table->string('experience_requise')->default('<1an');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offre_emplois', function (Blueprint $table) {
            $table->dropColumn([
                'lieu_travail',
                'document_annonce',
                'email_candidature',
                'url_candidature',
                'instructions_candidature',
                'documents_requis',
                'experience_requise',
            ]);
        });
    }
};
