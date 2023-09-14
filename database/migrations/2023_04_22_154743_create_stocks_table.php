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
        Schema::create('wb_stocks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('lastChangeDate')->nullable();
            $table->string('supplierArticle', 300)->nullable();
            $table->string('techSize', 50)->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->string('isSupply', 150)->nullable();
            $table->string('isRealization', 150)->nullable();
            $table->bigInteger('quantityFull')->nullable();
            $table->string('warehouseName', 150)->nullable();
            $table->bigInteger('nmId')->nullable();
            $table->string('subject', 150)->nullable();
            $table->string('category', 150)->nullable();
            $table->bigInteger('daysOnSite')->nullable();
            $table->string('brand', 150)->nullable();
            $table->string('SCCode', 150)->nullable();
            $table->bigInteger('Price')->nullable();
            $table->bigInteger('Discount')->nullable();
            $table->dateTime('dateUpdate')->nullable();
            $table->foreignId('lk_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
