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
        Schema::table('candidatures', function (Blueprint $table) {
            $table->longText('cv_genere')->nullable()->after('motivation_url');
            $table->longText('motivation_text')->nullable()->after('cv_genere');
            $table->decimal('note_ia', 5, 2)->nullable()->after('motivation_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn(['cv_genere', 'motivation_text', 'note_ia']);
        });
    }
};
