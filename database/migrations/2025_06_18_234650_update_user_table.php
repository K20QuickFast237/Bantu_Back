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
        Schema::table('users', function (Blueprint $table) {
            $table->after('name', function (Blueprint $table) {
                $table->string('prenom')->nullable();
            });

            $table->after('remember_token', function (Blueprint $table) {
                $table->string('photo_profil')->nullable();
                $table->string('role_actif')->nullable();  //->default('Particulier')->nullable();
                $table->boolean('is_active')->default(true);
                $table->dateTime('last_login')->nullable();
                $table->foreign('role_actif')->references('name')->on('roles')->onDelete('cascade');
            });
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
