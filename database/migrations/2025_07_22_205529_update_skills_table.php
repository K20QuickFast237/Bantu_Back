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
        Schema::table('skills', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->string('icon')->nullable()->change();
            $table->unsignedInteger('nbr_usage')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->string('description')->change();
            $table->string('icon')->change();
            $table->integer('nbr_usage')->change();
        });
    }
};
