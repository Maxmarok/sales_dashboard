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
        Schema::create('wb_orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->dateTime('lastChangeDate')->nullable();
            $table->text('supplierArticle')->nullable();
            $table->string('techSize', 80)->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->float('totalPrice')->nullable();
            $table->bigInteger('discountPercent')->nullable();
            $table->string('warehouseName', 150)->nullable();
            $table->string('oblast', 150)->nullable();
            $table->bigInteger('incomeID')->nullable();
            $table->bigInteger('odid')->nullable();
            $table->bigInteger('nmId')->nullable();
            $table->string('subject', 60)->nullable();
            $table->string('category', 60)->nullable();
            $table->string('brand', 150)->nullable();
            $table->string('isCancel', 10)->nullable();
            $table->string('cancel_dt', 100)->nullable();
            $table->string('gNumber', 100)->nullable();
            $table->string('sticker', 300)->nullable();
            $table->string('srid', 300)->nullable();
            $table->dateTime('dateUpdate')->nullable();

            $table->foreignId('lk_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
