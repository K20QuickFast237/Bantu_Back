<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['pending','approved','rejected','suspended'])->default('pending');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('shops');
    }
};