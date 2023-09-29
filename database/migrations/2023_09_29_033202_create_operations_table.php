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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['consume', 'profit'])->nullable();
            $table->bigInteger('value')->nullable();
            $table->foreignId('account_id')->references('id')->on('bank_accounts');
            $table->foreignId('article_id')->references('id')->on('articles');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->text('art')->nullable();
            $table->date('date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
