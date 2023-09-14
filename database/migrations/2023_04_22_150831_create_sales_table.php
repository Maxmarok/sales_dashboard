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
        Schema::create('wb_sales', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->dateTime('lastChangeDate')->nullable();
            $table->string('supplierArticle',80)->nullable();
            $table->string('techSize',80)->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->float('totalPrice')->nullable();
            $table->bigInteger('discountPercent')->nullable();
            $table->string('isSupply', 10)->nullable();
            $table->string('isRealization', 150)->nullable();
            $table->string('promoCodeDiscount', 150)->nullable();
            $table->string('warehouseName', 150)->nullable();
            $table->string('countryName', 150)->nullable();
            $table->string('oblastOkrugName', 300)->nullable();
            $table->string('regionName', 150)->nullable();
            $table->bigInteger('incomeID')->nullable();
            $table->string('saleID', 80)->nullable();
            $table->bigInteger('odid')->nullable();
            $table->float('spp')->nullable();
            $table->float('forPay')->nullable();
            $table->float('finishedPrice')->nullable();
            $table->float('priceWithDisc')->nullable();
            $table->bigInteger('nmId')->nullable();
            $table->string('subject', 60)->nullable();
            $table->string('category', 60)->nullable();
            $table->string('brand', 150)->nullable();
            $table->bigInteger('IsStorno')->nullable();
            $table->string('gNumber', 60)->nullable();
            $table->string('sticker', 300)->nullable();
            $table->string('srid', 300)->nullable();
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
        Schema::dropIfExists('sales');
    }
};
