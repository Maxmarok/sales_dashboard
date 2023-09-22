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
        Schema::create('wb_report_detail_by_periods', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('realizationreport_id')->nullable();
            $table->dateTime('dateFrom')->nullable();
            $table->dateTime('dateTo')->nullable();
            $table->bigInteger('nm_id')->nullable();
            $table->string('subject_name', 300)->nullable();
            $table->string('brand_name', 300)->nullable();
            $table->string('sa_name', 300)->nullable();
            $table->string('ts_name', 300)->nullable();
            $table->string('doc_type_name', 300)->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->bigInteger('retail_price')->nullable();
            $table->float('retail_amount')->nullable();
            $table->bigInteger('sale_percent')->nullable();
            $table->float('commission_percent')->nullable();
            $table->dateTime('order_dt')->nullable();
            $table->string('supplier_oper_name', 300)->nullable();
            $table->dateTime('sale_dt')->nullable();
            $table->float('retail_price_withdisc_rub')->nullable();
            $table->bigInteger('delivery_amount')->nullable();
            $table->float('delivery_rub')->nullable();
            $table->bigInteger('rid')->nullable();
            $table->float('ppvz_for_pay')->nullable();
            $table->float('ppvz_vw_nds')->nullable();
            $table->float('ppvz_sales_commission')->nullable();
            $table->bigInteger('additional_payment')->nullable();
            $table->float('penalty')->nullable();
            $table->string('lk', 60)->nullable();
            $table->dateTime('rr_dt')->nullable();
            $table->float('ppvz_spp_prc')->nullable();
            $table->string('office_name', 150)->nullable();
            $table->string('bonus_type_name', 150)->nullable();
            $table->bigInteger('rrd_id')->nullable();
            $table->bigInteger('return_amount')->nullable();

            $table->foreignId('lk_id');
            $table->dateTime('dateUpdate')->comment('Дата последнего обновления');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_detail_by_periods');
    }
};
