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
        Schema::create('user_roles', function (Blueprint $table) {
            // $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('role_id')->unsigned();
            $table->primary(['user_id', 'role_id']);
            $table->boolean('isCurrent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};
