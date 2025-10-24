<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // Modifier la colonne status pour ajouter le nouveau statut 'disabled'
            $table->enum('status', ['pending','approved','rejected','suspended','disabled'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // Revenir à l'ancien enum sans 'disabled'
            $table->enum('status', ['pending','approved','rejected','suspended'])
                  ->default('pending')
                  ->change();
        });
    }
};
