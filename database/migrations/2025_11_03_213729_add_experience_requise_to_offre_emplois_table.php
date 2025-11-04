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
            $table->enum('experience_requise', ['<1an', '1-3ans', '4-5ans', '>5ans'])
                  ->nullable()
                  ->after('responsabilites');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offre_emplois', function (Blueprint $table) {
            $table->dropColumn('experience_requise');
        });
    }
};
