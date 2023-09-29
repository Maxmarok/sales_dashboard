<?php
namespace App\Services\WB;

use App\Models\Lk;
use App\Models\WbReportDetailByPeriod;
use App\Models\WbSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WBSales{

    const MONTHS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec', 'all'];

    public function map_month(string $m): array
    {
        return [$m => 0];
    }

    public function dashboard()
    {
        $arr = [
            'sales' => WBSale::whereBetween('date', [now()->startOfMonth(), now()])->select(DB::raw("(DATE_FORMAT(date, '%Y-%m-%d')) as day"), DB::raw('round(sum(forPay), 2) as forPay'), DB::raw("(DATE_FORMAT(date, '%d.%m')) as date_short"))->groupBy('date_short', 'day')->get(),
            'reports' => WbReportDetailByPeriod::whereBetween('dateFrom', [now()->startOfMonth(), now()])->select(DB::raw("(DATE_FORMAT(dateFrom, '%Y-%m-%d')) as day"), DB::raw('round(sum(penalty), 2) as penalty_sum'), DB::raw('round(sum(delivery_rub), 2) as delivery_rub_sum'), DB::raw("(DATE_FORMAT(dateFrom, '%d.%m')) as date_short"))->groupBy('date_short', 'day')->get(),
        ];

        return $arr;
    }

    public function calendar()
    {
        $arr = [
            'sales' => WBSale::select(DB::raw("(DATE_FORMAT(date, '%Y-%m-%d')) as day"), DB::raw('round(sum(forPay), 2) as forPay'))->groupBy('day')->get(),
            'reports' => WbReportDetailByPeriod::select(DB::raw("(DATE_FORMAT(dateFrom, '%Y-%m-%d')) as day"), DB::raw('round(sum(penalty), 2) as penalty_sum'), DB::raw('round(sum(delivery_rub), 2) as delivery_rub_sum') )->groupBy('day')->get(),
        ];

        return $arr;
    }

    public function movements(?string $id, string $year)
    {
        $data = [
            'sales' => $this->sales($id, $year),
        ];

        $data = array_merge($this->reports($id, $year, $data['sales']), $data);
        
        foreach(self::MONTHS as $month) {
            if($month === 'all') {
                $data['all'][$month] = $data['sales']['all'] - $data['consume']['all'];
            } else {
                $data['all'][$month] = $data['sales'][$month] - $data['consume'][$month];
            }
        }

        return Response()->json([
            'data' => $data
        ]);
    }

    public function reports(?string $id, string $year, array $sales)
    {
        $res = WbReportDetailByPeriod::whereYear('dateFrom', $year);

        if($id) {
            $lks = Lk::where('id', $id)->get();
            $res = $res->where('lk_id', $id);
        } else {
            $lks = Lk::where('user_id', auth()->id())->get();
        }

        $data = [
            'commission' => null,
            'penalty' => null,
            'delivery' => null,
            'consume' => null,
        ];

        foreach(self::MONTHS as $month) {
            foreach($data as $key => $val) {
                $data[$key][$month] = 0;
            }
        }

        foreach ($lks as $lk) {
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

            $commission = [
                'jan' => $lk->tax > 0 ? $sales['jan'] / $lk->tax : 0,
                'feb' => $lk->tax > 0 ? $sales['feb'] / $lk->tax : 0,
                'mar' => $lk->tax > 0 ? $sales['mar'] / $lk->tax : 0,
                'apr' => $lk->tax > 0 ? $sales['apr'] / $lk->tax : 0,
                'may' => $lk->tax > 0 ? $sales['may'] / $lk->tax : 0,
                'jun' => $lk->tax > 0 ? $sales['jun'] / $lk->tax : 0,
                'jul' => $lk->tax > 0 ? $sales['jul'] / $lk->tax : 0,
                'aug' => $lk->tax > 0 ? $sales['aug'] / $lk->tax : 0,
                'sep' => $lk->tax > 0 ? $sales['sep'] / $lk->tax : 0,
                'oct' => $lk->tax > 0 ? $sales['oct'] / $lk->tax : 0,
                'nov' => $lk->tax > 0 ? $sales['nov'] / $lk->tax : 0,
                'dec' => $lk->tax > 0 ? $sales['dec'] / $lk->tax : 0,
                'all' => $lk->tax > 0 ? $sales['all'] / $lk->tax : 0,
            ];

            $penalty = [
                'jan' => $jan->whereMonth('dateFrom', '01')->sum('penalty'),
                'feb' => $feb->whereMonth('dateFrom', '02')->sum('penalty'),
                'mar' => $mar->whereMonth('dateFrom', '03')->sum('penalty'),
                'apr' => $apr->whereMonth('dateFrom', '04')->sum('penalty'),
                'may' => $may->whereMonth('dateFrom', '05')->sum('penalty'),
                'jun' => $jun->whereMonth('dateFrom', '06')->sum('penalty'),
                'jul' => $jul->whereMonth('dateFrom', '07')->sum('penalty'),
                'aug' => $aug->whereMonth('dateFrom', '08')->sum('penalty'),
                'sep' => $sep->whereMonth('dateFrom', '09')->sum('penalty'),
                'oct' => $oct->whereMonth('dateFrom', '10')->sum('penalty'),
                'nov' => $nov->whereMonth('dateFrom', '11')->sum('penalty'),
                'dec' => $dec->whereMonth('dateFrom', '12')->sum('penalty'),
                'all' => $all->sum('penalty'),
            ];

            $delivery = [
                'jan' => $jan->whereMonth('dateFrom', '01')->sum('delivery_rub'),
                'feb' => $feb->whereMonth('dateFrom', '02')->sum('delivery_rub'),
                'mar' => $mar->whereMonth('dateFrom', '03')->sum('delivery_rub'),
                'apr' => $apr->whereMonth('dateFrom', '04')->sum('delivery_rub'),
                'may' => $may->whereMonth('dateFrom', '05')->sum('delivery_rub'),
                'jun' => $jun->whereMonth('dateFrom', '06')->sum('delivery_rub'),
                'jul' => $jul->whereMonth('dateFrom', '07')->sum('delivery_rub'),
                'aug' => $aug->whereMonth('dateFrom', '08')->sum('delivery_rub'),
                'sep' => $sep->whereMonth('dateFrom', '09')->sum('delivery_rub'),
                'oct' => $oct->whereMonth('dateFrom', '10')->sum('delivery_rub'),
                'nov' => $nov->whereMonth('dateFrom', '11')->sum('delivery_rub'),
                'dec' => $dec->whereMonth('dateFrom', '12')->sum('delivery_rub'),
                'all' => $all->sum('delivery_rub'),
            ];

            $consume = [
                'jan' => $commission['jan'] + $penalty['jan'] + $delivery['jan'],
                'feb' => $commission['feb'] + $penalty['feb'] + $delivery['feb'],
                'mar' => $commission['mar'] + $penalty['mar'] + $delivery['mar'],
                'apr' => $commission['apr'] + $penalty['apr'] + $delivery['apr'],
                'may' => $commission['may'] + $penalty['may'] + $delivery['may'],
                'jun' => $commission['jun'] + $penalty['jun'] + $delivery['jun'],
                'jul' => $commission['jul'] + $penalty['jul'] + $delivery['jul'],
                'aug' => $commission['aug'] + $penalty['aug'] + $delivery['aug'],
                'sep' => $commission['sep'] + $penalty['sep'] + $delivery['sep'],
                'oct' => $commission['oct'] + $penalty['oct'] + $delivery['oct'],
                'nov' => $commission['nov'] + $penalty['nov'] + $delivery['nov'],
                'dec' => $commission['dec'] + $penalty['dec'] + $delivery['dec'],
                'all' => $commission['all'] + $penalty['all'] + $delivery['all'],
            ];

            //Log::debug($data['commission']);

            foreach(self::MONTHS as $month) {
                $data['commission'][$month] += $commission[$month];
                $data['penalty'][$month] += $penalty[$month];
                $data['delivery'][$month] += $delivery[$month];
                $data['consume'][$month] += $consume[$month];
            }

            
        }

        return $data;
    }

    public function sales(?string $id, string $year)
    {

        $res = WBSale::whereYear('date', $year);

        if($id) {
            $res = $res->where('lk_id', $id);
        }

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
            'all' => $all->sum('forPay'),
        ];

        return $arr;
    }
}
