<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('shop_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_method_id')->constrained()->onDelete('cascade');
            $table->decimal('price',10,2)->default(0);
            $table->string('duration')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('shop_delivery');
    }
};
