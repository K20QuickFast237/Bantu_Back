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
            $table->after('name', function (Blueprint $table){
                $table->string('prenom')->nullable();
            });
            
            $table->after('remember_token', function (Blueprint $table){
                $table->enum('role', ['Admin', 'Professionnel', 'Particulier'])->default('Particulier');
                $table->boolean('is_active')->default(true);
                $table->dateTime('last_login')->nullable();
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
