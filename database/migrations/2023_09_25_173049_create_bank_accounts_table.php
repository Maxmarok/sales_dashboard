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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->text('title')->nullable();
            $table->text('bank')->nullable();
            $table->text('bic')->nullable();
            $table->text('ks')->nullable();
            $table->text('number')->nullable();
            $table->enum('currency', ['RUB', 'KZT', 'BYR'])->nullable();
            $table->text('balance')->nullable();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('lk_id')->references('id')->on('lks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
