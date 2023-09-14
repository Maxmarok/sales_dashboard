<?php
namespace App\Services\WB;

use App\Models\WbOrder;
use App\Models\WbReportDetailByPeriod;
use App\Models\WbSale;
use App\Models\WbStock;

class DataService extends WBMain{

    private int $user_id;

    public function __construct($user_id = null)
    {
        $this->user_id = $user_id == null ? Auth()->id() : $user_id;
    }

    /**
     * Отчет за период
     * @param string $key
     * @param string $dateFrom
     * @param string $dateTo
     * @return bool
     */
    public function reportDetailByPeriod(string $key, string $dateFrom, string $dateTo): bool
    {
        $data = [
            "headers" => ["Authorization" => $key],
            "query" => [
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo
            ]
        ];

        $request = $this->sendRequest('statistic','GET', '/api/v1/supplier/reportDetailByPeriod', $data);
        //если произошла ошибка
        if(get_class($request) == 'Illuminate\Http\JsonResponse'){
            return false;
        }
        $response = json_decode($request->getBody());

        if($response == null){
            return false;
        }

        //каждую неделю обновляем
        foreach ($response as $row){
            WbReportDetailByPeriod::create([
                "realizationreport_id" => $row->realizationreport_id,
                "dateFrom" => $row->date_from,
                "dateTo" => $row->date_to,
                "nm_id" => $row->nm_id,
                "subject_name" => $row->subject_name,
                "brand_name" => $row->brand_name,
                "sa_name" => $row->sa_name,
                "ts_name" => $row->ts_name,
                "doc_type_name" => $row->doc_type_name,
                "barcode" => $row->barcode,
                "quantity" => $row->quantity,
                "retail_price" => $row->retail_price,
                "retail_amount" => $row->retail_amount,
                "sale_percent" => $row->sale_percent,
                "commission_percent" => $row->commission_percent,
                "order_dt" => $row->order_dt,
                "supplier_oper_name" => $row->supplier_oper_name,
                "sale_dt" => $row->sale_dt,
                "retail_price_withdisc_rub" => $row->retail_price_withdisc_rub,
                "delivery_amount" => $row->delivery_amount,
                "delivery_rub" => $row->delivery_rub,
                "rid" => $row->rid,
                "ppvz_for_pay" => $row->ppvz_for_pay,
                "ppvz_vw_nds" => $row->ppvz_vw_nds,
                "ppvz_sales_commission" => $row->ppvz_sales_commission,
                "additional_payment" => $row->additional_payment,
                "penalty" => $row->penalty,
                "dateUpdate" => $row->date_update ?? null,
                "lk" => $row->lk ?? null,
                "rr_dt" => $row->rr_dt,
                "ppvz_spp_prc" => $row->ppvz_spp_prc,
                "office_name" => $row->office_name,
                "bonus_type_name" => $row->bonus_type_name ?? null,
                "rrd_id" => $row->rrd_id,
                "user_id" => $this->user_id
            ]);
        }
        return true;
    }

    /**
     * @param string $key
     * @param string $dateFrom
     * @return bool
     */
    public function sales(string $key, string $dateFrom): bool
    {
        $data = [
            "headers" => ["Authorization" => $key],
            "query" => [
                'dateFrom' => $dateFrom,
            ]
        ];

        $request = $this->sendRequest('statistic','GET', '/api/v1/supplier/sales', $data);
        //если произошла ошибка
        if(get_class($request) == 'Illuminate\Http\JsonResponse'){
            return false;
        }

        $response = json_decode($request->getBody());

        //каждую неделю обновляем
        foreach ($response as $row){
            WbSale::create([
                "date" => $row->date,
                "lastChangeDate" => $row->lastChangeDate,
                "supplierArticle" => $row->supplierArticle,
                "techSize" => $row->techSize,
                "barcode" => $row->barcode,
                "totalPrice" => $row->totalPrice,
                "discountPercent" => $row->discountPercent,
                "isSupply" => $row->isSupply,
                "isRealization" => $row->isRealization,
                "promoCodeDiscount" => $row->promoCodeDiscount,
                "warehouseName" => $row->warehouseName,
                "countryName" => $row->countryName,
                "oblastOkrugName" => $row->oblastOkrugName,
                "regionName" => $row->regionName,
                "incomeID" => $row->incomeID,
                "saleID" => $row->saleID,
                "odid" => $row->odid,
                "spp" => $row->spp,
                "forPay" => $row->forPay,
                "finishedPrice" => $row->finishedPrice,
                "priceWithDisc" => $row->priceWithDisc,
                "nmId" => $row->nmId,
                "subject" => $row->subject,
                "category" => $row->category,
                "brand" => $row->brand,
                "IsStorno" => $row->IsStorno,
                "gNumber" => $row->gNumber,
                "sticker" => $row->sticker,
                "srid" => $row->srid,
                "dateUpdate" => now(),
                "lk" => $row->lk ?? null,
                "user_id" => $this->user_id
            ]);
        }
        return true;
    }

    /**
     * @param $key
     * @param $dateFrom
     * @return bool
     */
    public function orders($key, $dateFrom): bool
    {
        $data = [
            "headers" => ["Authorization" => $key],
            "query" => [
                'dateFrom' => $dateFrom,
            ]
        ];

        $request = $this->sendRequest('statistic','GET', '/api/v1/supplier/orders', $data);
        //если произошла ошибка
        if(get_class($request) == 'Illuminate\Http\JsonResponse'){
            return false;
        }

        $response = json_decode($request->getBody());

        foreach ($response as $row){
            WbOrder::create([
                'date' => $row->date,
                'lastChangeDate' => $row->lastChangeDate,
                'supplierArticle' => $row->supplierArticle,
                'techSize' => $row->techSize,
                'barcode' => $row->barcode,
                'totalPrice' => $row->totalPrice,
                'discountPercent' => $row->discountPercent,
                'warehouseName' => $row->warehouseName,
                'oblast' => $row->oblast,
                'incomeID' => $row->incomeID,
                'odid' => $row->odid,
                'nmId' => $row->nmId,
                'subject' => $row->subject,
                'category' => $row->category,
                'brand' => $row->brand,
                'isCancel' => $row->isCancel,
                'cancel_dt' => $row->cancel_dt,
                'gNumber' => $row->gNumber,
                'sticker' => $row->sticker,
                'user_id' => $this->user_id
            ]);
        }

        return true;
    }

    /**
     * @param $key
     * @param $dateFrom
     * @return bool
     */
    public function stocks($key, $dateFrom): bool
    {
        $data = [
            "headers" => ["Authorization" => $key],
            "query" => [
                'dateFrom' => $dateFrom,
            ]
        ];

        $request = $this->sendRequest('statistic','GET', '/api/v1/supplier/stocks', $data);
        //если произошла ошибка
        if(get_class($request) == 'Illuminate\Http\JsonResponse'){
            return false;
        }

        $response = json_decode($request->getBody());

        foreach ($response as $row){
            WbStock::create([
                'lastChangeDate' => $row->lastChangeDate,
                'supplierArticle' => $row->supplierArticle,
                'techSize' => $row->techSize,
                'barcode' => $row->barcode,
                'quantity' => $row->quantity,
                'isSupply' => $row->isSupply,
                'isRealization' => $row->isRealization,
                'quantityFull' => $row->quantityFull,
                'warehouseName' => $row->warehouseName,
                'nmId' => $row->nmId,
                'subject' => $row->subject,
                'category' => $row->category,
                'daysOnSite' => $row->daysOnSite,
                'brand' => $row->brand,
                'SCCode' => $row->SCCode,
                'Price' => $row->Price,
                'Discount' => $row->Discount,
                'dateUpdate' => now(),
                'user_id' => $this->user_id
            ]);
        }

        return true;
    }
}
