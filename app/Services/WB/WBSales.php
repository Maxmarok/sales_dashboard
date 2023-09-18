<?php
namespace App\Services\WB;

use App\Models\Lk;
use App\Models\WbSale;
use Illuminate\Support\Facades\DB;

class WBSales{

    public function movements(string $id, string $year)
    {
        $data = [
            'sales' => $this->sales($id, $year),
        ];

        return Response()->json([
            'data' => $data
        ]);
    }

    public function sales(string $id, string $year)
    {
        // $user = auth()->user();
        // $lk = Lk::find($id);

        // if(!$lk) return Response()->json([
        //     'code' => 422,
        //     'message' => 'Не найден магазин'
        // ]);

        //$year = '2023';
        
        $res = WBSale::where('lk_id', $id)->whereYear('date', $year);
        $jan = clone $res;
        $feb = clone $res;
        $mar = clone $res;
        $apr = clone $res;
        $may = clone $res;
        $jun = clone $res;
        $jul = clone $res;
        $aug = clone $res;
        $sep = clone $res;
        $oct = clone $res;
        $nov = clone $res;
        $dec = clone $res;
        $all = clone $res;
        

        $arr = [
            'all' => $all->sum('forPay'),
            'jan' => $jan->whereMonth('date', '01')->sum('forPay'),
            'feb' => $feb->whereMonth('date', '02')->sum('forPay'),
            'mar' => $mar->whereMonth('date', '03')->sum('forPay'),
            'apr' => $apr->whereMonth('date', '04')->sum('forPay'),
            'may' => $may->whereMonth('date', '05')->sum('forPay'),
            'jun' => $jun->whereMonth('date', '06')->sum('forPay'),
            'jul' => $jul->whereMonth('date', '07')->sum('forPay'),
            'aug' => $aug->whereMonth('date', '08')->sum('forPay'),
            'sep' => $sep->whereMonth('date', '09')->sum('forPay'),
            'oct' => $oct->whereMonth('date', '10')->sum('forPay'),
            'nov' => $nov->whereMonth('date', '11')->sum('forPay'),
            'dec' => $dec->whereMonth('date', '12')->sum('forPay'),
            // 'jun' => $res->whereRaw('MONTH(date) = ?',[06])->sum('forPay'),
            // 'aug' => $res->whereRaw('MONTH(date) = ?',[06])->sum('forPay'),
        ];

        foreach($arr as &$item) $item = number_format(round($item, 0), 0, '', ' ') . ' ₽';

        // $arr = [
        //     'totalPrice' => 0,
        //     'forPay' => 0,
        //     'finishedPrice' => 0,
        //     'priceWithDisc' => 0,
        // ];

        // foreach ($res as $row){
        //     $arr['totalPrice'] += $row->totalPrice;
        //     $arr['forPay'] += $row->forPay;
        //     $arr['finishedPrice'] += $row->finishedPrice;
        //     $arr['priceWithDisc'] += $row->priceWithDisc;
        // }

        //foreach($arr as &$item) $item = round($item);

        return $arr;
    }
}
