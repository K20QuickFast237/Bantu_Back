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
        Schema::table('particuliers', function (Blueprint $table) {
            $table->string('portfolio_link')->nullable()->after('lettre_motivation_link');
            $table->string('linkedin_link')->nullable()->after('portfolio_link');
            $table->string('behance_link')->nullable()->after('linkedin_link');
            $table->string('github_link')->nullable()->after('behance_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('particuliers', function (Blueprint $table) {
            $table->dropColumn(['portfolio_link', 'linkedin_link', 'behance_link', 'github_link']);
        });
    }
};
