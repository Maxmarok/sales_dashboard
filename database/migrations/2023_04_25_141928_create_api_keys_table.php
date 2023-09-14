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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->enum('marketplace', ['WB', 'OZ', 'YA']);
            $table->enum('type', ['statistic','standard','ad'])->comment('Тип ключа: стандартный, статистика, реклама');
            $table->foreignId('lk_id')->references('id')->on('lks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
