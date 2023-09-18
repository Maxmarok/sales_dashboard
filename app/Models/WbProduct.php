<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbProduct extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getForCurrentUser(int $lk_id) : array
    {
        $products = WbProduct::where('lk_id', $lk_id)->orderBy('id', 'ASC')->get();

        return array_map(function ($product) {
            return [
                'id' => $product['id'],
                'sizes' => json_decode($product['sizes']),
                'mediaFiles' => json_decode($product['mediaFiles']),
                'colors' => json_decode($product['colors']),
                'price' => WbPrice::where('nmId', $product['nmID'])->pluck('price')->first() ?? null,
                'orders' => WbOrder::where('nmId', $product['nmID'])->count(), //Заказы шт
                'returns' => WbSale::where('nmId', $product['nmID'])->where('saleID', 'LIKE', 'R%')->count(), //Возвраты шт
                'revenue' => '???', //Выручка
                'costPrice' => $product['costPrice'], //Себестоимость
                'updateAt' => $product['updateAt'],
                'vendorCode' => $product['vendorCode'],
                'brand' => $product['brand'],
                'object' => $product['object'],
                'nmID' => $product['nmID'],
                'imtID' => $product['imtID'],
                'isProhibited' => $product['isProhibited'],
                'tags' => json_decode($product['tags']),
                'lk' => Lk::find($product['lk_id'])->name
            ];
        }, $products->toArray());
    }
}
