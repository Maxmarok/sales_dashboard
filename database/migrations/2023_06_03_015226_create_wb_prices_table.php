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
        Schema::create('wb_prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nmId');
            $table->bigInteger('price');
            $table->bigInteger('discount');
            $table->bigInteger('promoCode');
            $table->foreignId('lk_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_prices');
    }
};
