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
    Schema::table('tags', function (Blueprint $table) {
        $table->renameColumn('tag_name', 'name');
        $table->string('slug')->unique()->after('name');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::table('tags', function (Blueprint $table) {
        $table->dropColumn('slug');
        $table->dropTimestamps();
        $table->renameColumn('name', 'tag_name');
    });
}
};
