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
        Schema::create('wb_products', function (Blueprint $table) {
            $table->id();
            $table->json('sizes');
            $table->json('mediaFiles');
            $table->json('colors');
            $table->timestampTz('updateAt');
            $table->string('vendorCode');
            $table->string('brand');
            $table->string('object');
            $table->string('nmID');
            $table->string('imtID');
            $table->string('isProhibited');
            $table->json('tags');
            $table->unsignedBigInteger('costPrice')->nullable()->comment('Себестоимость');
            $table->foreignId('lk_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wb_products');
    }
};
