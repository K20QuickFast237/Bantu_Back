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
        Schema::create('mkt_produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendeur_id')->constrained('mkt_vendeurs')->cascadeOnDelete();
            $table->foreignId('categorie_id')->nullable()->constrained('mkt_categorie_produits');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('prix', 12, 2);
            $table->integer('stock_qtte')->default(0);
            $table->boolean('hasAttributes')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mkt_produits');
    }
};
