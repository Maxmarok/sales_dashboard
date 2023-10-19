<?php
namespace App\Services\WB;

use App\Models\Articles;
use App\Models\Lk;
use App\Models\Operations;
use App\Models\WbReportDetailByPeriod;
use App\Models\WbSale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Constraint\Operator;

class WBSales{

    const MONTHS = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', 'all'];

    public function map_month(string $m): array
    {
        return [$m => 0];
    }

    public function dashboard()
    {
        $arr = [
            'sales' => WBSale::whereBetween('date', [now()->subWeeks(2), now()])->select(DB::raw("(DATE_FORMAT(date, '%Y-%m-%d')) as day"), DB::raw('round(sum(forPay), 2) as forPay'), DB::raw("(DATE_FORMAT(date, '%d.%m')) as date_short"))->groupBy('date_short', 'day')->get(),
            'reports' => WbReportDetailByPeriod::whereBetween('dateFrom', [now()->subWeeks(2), now()])->select(DB::raw("(DATE_FORMAT(dateFrom, '%Y-%m-%d')) as day"), DB::raw('round(sum(penalty), 2) as penalty_sum'), DB::raw('round(sum(delivery_rub), 2) as delivery_rub_sum'), DB::raw("(DATE_FORMAT(dateFrom, '%d.%m')) as date_short"))->groupBy('date_short', 'day')->get(),
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

    public function expenses(?string $id, string $year)
    {
        $cashflow = $this->getCashflowData();
        $movements = $this->getMovementsData($id, $year);

        Log::debug([$cashflow, $movements]);


        $data = array_merge($cashflow, $movements);

        foreach(self::MONTHS as $month) {
            //$data[];

            $profit = $data['profit'][$month] + $data['sales'][$month];
            $consume = $data['consume'][$month];


            $data['expenses'][$month] = $profit > 0 ? ($consume / $profit * 100) : 0;
        }

        return Response()->json([
            'data' => $data,
        ]);
    }

    public function cashflow()
    {
        $data = $this->getCashflowData();

        return Response()->json([
            'data' => $data,
        ]);
    }

    public function getCashflowData()
    {
        $cache = 'cashflow_'.auth()->id();

        if(!Cache::has($cache)) {
            $profit = $consume = $sum = [];

            foreach(self::MONTHS as $month) {
                $profit[$month] = 0;
                $consume[$month] = 0;
                $sum[$month] = 0;
            }

            $res = Operations::where('operations.user_id', auth()->id())
            ->leftJoin('bank_accounts as account', 'account_id', 'account.id')
            ->groupBy('account.currency', 'type')
            ->select('account.currency', 'operations.type', DB::raw('round(sum(value), 2) as value'));

            foreach(self::MONTHS as $month) {
                $item = clone $res;

                if($month === 'all') {
                    $data[$month] = $item->where('type', 'profit')->get();
                } else {
                    $data[$month] = $item->whereMonth('date', $month)->where('type', 'profit')->get();
                }
            }

            foreach($data as $key => $value) {
                foreach($value as $item) {
                    if($item['currency'] === 'KZT') {
                        $profit[$key] += round($item['value'] / 4.5, 0);
                    } elseif($item['currency'] === 'BYR') {
                        $profit[$key] += round($item['value'] / 0.25, 0);
                    } else {
                        $profit[$key] += $item['value'];
                    }
                }
            }

            foreach(self::MONTHS as $month) {
                $item = clone $res;

                if($month === 'all') {
                    $data[$month] = $item->where('type', 'profit')->get();
                } else {
                    $data[$month] = $item->whereMonth('date', $month)->where('type', 'consume')->get();
                }
            }

            foreach($data as $key => $value) {
                foreach($value as $item) {
                    if($item['currency'] === 'KZT') {
                        $consume[$key] += round($item['value'] / 5, 0);
                    } elseif($item['currency'] === 'BYR') {
                        $consume[$key] += round($item['value'] * 0,25, 0);
                    } else {
                        $consume[$key] += $item['value'];
                    }
                }
            }

            foreach($sum as $k => $v) {
                $sum[$k] = $profit[$k] - $consume[$k];
            }
            

            $operations = [
                'profit' => $profit,
                'consume' => $consume,
                'sum' => $sum,
            ];

            Cache::put('cashflow_'.auth()->id(), $operations, 10);
        }

        return Cache::get($cache);
    }

    public function movements(?string $id, string $year)
    {
        return Response()->json([
            'data' => $this->getMovementsData($id, $year),
        ]);
    }

    public function getMovementsData(?string $id, string $year)
    {
        $cache = 'movements_'.auth()->id().'_'.$year.'_'.$id;

        if(!Cache::has($cache)) {
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

            Cache::put('movements_'.auth()->id().'_'.$year.'_'.$id, $data, 7200);
        } 

        return Cache::get($cache);
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

            foreach(self::MONTHS as $month) {
                $item = clone $res;
                $commission[$month] = $lk->tax > 0 ? $sales[$month] / $lk->tax : 0;


                if($month === 'all') {
                    $penalty[$month] = $item->sum('penalty');
                    $delivery[$month] = $item->sum('delivery_rub');

                } else {
                    $penalty[$month] = $item->whereMonth('dateFrom', $month)->sum('penalty');
                    $delivery[$month] = $item->whereMonth('dateFrom', $month)->sum('delivery_rub');
                }

                $consume[$month] = $commission[$month] + $penalty[$month] + $delivery[$month];
            
            }

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

        foreach(self::MONTHS as $month) {
            $item = clone $res;

            if($month === 'all') {
                $arr[$month] = $item->sum('forPay');
            } else {
                $arr[$month] = $item->whereMonth('date', $month)->sum('forPay');
            }
        }

        return $arr;
    }
}
