<?php
namespace App\Services\WB;

use App\Http\Requests\SumController\SumRequest;
use App\Models\WbOrder;
use App\Models\WbReportDetailByPeriod;
use App\Models\WbSale;
use App\Models\WbStock;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use phpseclib3\Math\PrimeField\Integer;
use Revolution\Google\Sheets\Facades\Sheets;

class Calculations{
    private $WbReportDetailByPeriodTable;
    private $WbSalesTable;
    private $WbOrdersTable;

    private $tax = 7; //Налог

    public function __construct()
    {
        $this->WbReportDetailByPeriodTable = (new WbReportDetailByPeriod)->getTable();
        $this->WbSalesTable = (new WbSale())->getTable();
        $this->WbOrdersTable = (new WbOrder())->getTable();
    }

    //Самовыкупы
    public function buyouts($type)
    {
        //$type = коллво или сумма
        return 0;
    }

    private function getCalc($calc, $dateFrom, $dateTo, $lk_id){
        switch ($calc){
            case 'calc1':
                return WbReportDetailByPeriod::where('supplier_oper_name', 'Продажа')
                    ->orWhere('supplier_oper_name', 'Корректная продажа')
                    ->orWhere('supplier_oper_name', 'Сторно возвратов')
                    ->where('lk_id', $lk_id)
                    ->where('dateFrom', $dateFrom)
                    ->where('dateTo', $dateTo)
                    ->sum('retail_amount');
            case 'calc2':
                return WbReportDetailByPeriod::where('supplier_oper_name', 'Возврат')
                    ->orWhere('supplier_oper_name', 'Корректный возврат')
                    ->orWhere('supplier_oper_name', 'Сторно продаж')
                    ->where('lk_id', $lk_id)
                    ->where('dateFrom', $dateFrom)
                    ->where('dateTo', $dateTo)
                    ->sum('retail_amount');
            case 'calc3':
                return WbReportDetailByPeriod::where('supplier_oper_name', 'Оплата брака')
                    ->orWhere('supplier_oper_name', 'Оплата потерянного товара')
                    ->orWhere('supplier_oper_name', 'Оплата по итогам инвентаризации')
                    ->where('lk_id', $lk_id)
                    ->where('dateFrom', $dateFrom)
                    ->where('dateTo', $dateTo)
                    ->sum('retail_amount');
            case 'calc4':
                return WbSale::whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()])
                    ->where('lk_id', $lk_id)
                    ->sum('finishedPrice');
            default:
                return null;
        }
    }

    /**
     * Выручка
     * @return JsonResponse
     */
    public function revenue($dateFrom, $dateTo, $lk_id)
    {
        try {
            // Вычисляем значения для расчетов
            $calculation1 = $this->getCalc('calc1', $dateFrom, $dateTo, $lk_id);
            $calculation2 = $this->getCalc('calc2', $dateFrom, $dateTo, $lk_id);
            $calculation3 = $this->getCalc('calc3', $dateFrom, $dateTo, $lk_id);
            $calculation4 = $this->getCalc('calc4', $dateFrom, $dateTo, $lk_id);
            $calculation5 = $this->buyouts('sum');

            // Вычисляем общую выручку
            $revenue = $calculation1 + $calculation3 + $calculation4 - $calculation2 - $calculation5;

            return response()->json(["revenue" => round($revenue)]);
        }catch (\Exception $e){
            return response()->json(["revenue" => null]);
        }
    }

    /**
     * Орг Выручка
     * @param $dateFrom
     * @param $dateTo
     * @param $lk_id
     * @return JsonResponse
     */
    public function orgRevenue(SumRequest $request): JsonResponse
    {
        $sales = WbSale::where('lk_id', $request->lk_id)
            ->whereBetween('date', [$request->dateFrom, $request->dateTo])
        ->sum('totalPrice');

        $returns = WbSale::where('lk_id', $request->lk_id)
            ->whereBetween('date', [$request->dateFrom, $request->dateTo])
            ->where('saleID', 'LIKE', 'R%')
            ->sum('totalPrice');

        $buyouts = $this->buyouts('sum');

        return Response()->json([
            "orgRevenue" => $sales - $returns - $buyouts
        ]);
    }

    /**
     * логистика
     */
    public function logistics($dateFrom, $dateTo, $lk_id)
    {
        try {
            $calc1 = $this->getCalc('calc1', $dateFrom, $dateTo, $lk_id);
            $calc2 = $this->getCalc('calc2', $dateFrom, $dateTo, $lk_id);
            return Response()->json(['logistics' => round($calc1 - $calc2)]);
        }catch (\Exception $e){
            return Response()->json(['logistics' => null]);
        }
    }

    public function logisticsPercent($dateFrom, $dateTo, $lk_id)
    {
        try {
            $logistics = collect($this->logistics($dateFrom, $dateTo, $lk_id)->original)['logistics'];
            $revenue = collect($this->revenue($dateFrom, $dateTo, $lk_id)->original)['revenue'];
            #TODO почему-то они одинаковые

            if ($logistics == 0 || $revenue == 0) {
                return Response()->json([
                    'logisticsPercent' => 0
                ]);
            }

            return Response()->json(['logisticsPercent' => $logistics / $revenue]);
        }catch (\Exception $e){
            return Response()->json(['logisticsPercent' => null]);
        }
    }

    //комиссия
    public function commission($dateFrom, $dateTo, $lk_id)
    {
        try {
            $result = DB::table($this->WbReportDetailByPeriodTable)
                ->select(DB::raw('
              (SUM(CASE WHEN supplier_oper_name IN (\'Продажа\', \'Корректная продажа\', \'Сторно возвратов\') THEN ppvz_vw_nds ELSE 0 END)
                + SUM(ppvz_sales_commission)
              )

              -

              (SUM(CASE WHEN supplier_oper_name IN (\'Возврат\', \'Корректный возврат\', \'Сторно продаж\') THEN ppvz_vw_nds ELSE 0 END)
                + SUM(ppvz_sales_commission)
              ) as commission'))
                ->get();

            return Response()->json([
                'commission' => round($result[0]->commission)
            ]);
        }catch (\Exception $e){
            return Response()->json(['commission' => null]);
        }
    }

    public function commissionPercent($dateFrom, $dateTo, $lk_id)
    {
        try {
            $commission = collect($this->commission($dateFrom, $dateTo, $lk_id)->original)['commission'];
            $revenue = collect($this->revenue($dateFrom, $dateTo, $lk_id)->original)['revenue'];

            if ($commission == 0 || $revenue == 0) {
                return Response()->json([
                    'commissionPercent' => 0
                ]);
            }

            return Response()->json([
                'commissionPercent' => $commission / $revenue
            ]);
        }catch (\Exception $e){
            return Response()->json(['commissionPercent' => null]);
        }
    }

    //маржа
    public function margin($dateFrom, $dateTo, $lk_id)
    {
        try {
            $revenue = collect($this->revenue($dateFrom, $dateTo, $lk_id)->original)['revenue'] ?? 0;
            $logistics = collect($this->logistics($dateFrom, $dateTo, $lk_id)->original)['logistics'] ?? 0;
            $commission = collect($this->commission($dateFrom, $dateTo, $lk_id)->original)['commission'] ?? 0;
            $buyouts = $this->buyouts('sum');

            //NewCode
            $ad = $this->adv($dateFrom, $dateTo, $lk_id);
            $tax = ($this->tax / 100) * $revenue;
            //орг. выручка - комиссия - логистика - себестоимость - реклама - налог 7%

            return Response()->json([
                'margin' => round($revenue - $logistics - $commission - $buyouts - $ad - $tax)
            ]);
        }catch (\Exception $e){
            return Response()->json([
                'margin' => null
            ]);
        }
    }

    //Скидка постоянного покупателя
    public function regularCustomerDiscount($dateFrom, $dateTo, $lk_id)
    {
        //цена - ppvz_spp_prc %
        $query = DB::table($this->WbReportDetailByPeriodTable)
            ->where('dateFrom', '>=', $dateFrom)
            ->where('dateTo', '<=', $dateTo)
            ->where('lk_id', $lk_id)
            ->get();

        if ($query->count() > 0) {
            $totalDiscountPercentage = 0;

            foreach ($query as $reportDetail) {
                $retailPrice = $reportDetail->retail_price;
                $ppvzSppPrc = $reportDetail->ppvz_spp_prc;
                $discountPercentage = $retailPrice - $ppvzSppPrc;

                // Накапливаем общий процент скидки
                $totalDiscountPercentage += $discountPercentage;
            }

            // Рассчитываем средний процент скидки
            $averageDiscountPercentage = $totalDiscountPercentage / $query->count();
        }

        if($query->isEmpty()){
            $response = ['regularCustomerDiscount' => null];
        }else{
            $response = ['regularCustomerDiscount' => round($averageDiscountPercentage)];
        }

        return Response()->json($response);
    }

    function getFieldSumFromPeriodTable($fieldName, $dateFrom, $dateTo, $lk_id)
    {
        $total = DB::table($this->WbReportDetailByPeriodTable)
            ->where('dateFrom', '>=', $dateFrom)
            ->where('dateTo', '<=', $dateTo)
            ->where('lk_id', $lk_id)
            ->select(DB::raw('SUM('.$fieldName.') as total'))
            ->get();

        if($total == null){
            $response = ['total' => null];
        }else{
            $response = ['total' => $total[0]->total];
        }

        return Response()->json($response);
    }

    //SPP
    public function spp(string $dateFrom, string $dateTo, $lk_id): JsonResponse
    {
        $spp = DB::table($this->WbReportDetailByPeriodTable)
            ->where('lk_id', $lk_id)
            ->where('dateFrom', '>=', $dateFrom)
            ->where('dateTo', '<=', $dateTo)
            ->select(DB::raw('sum(retail_price_withdisc_rub - ppvz_for_pay) as spp'))
            ->get();
        return Response()->json(['spp' => round($spp[0]->spp)]);
    }

    //Прогноз выручки
    public function revenueForecast(string $dateFrom, string $dateTo, $lk_id): JsonResponse
    {
        $firstDate = Carbon::parse($dateFrom);
        $secondDate = Carbon::parse($dateTo);
        //Разница в дате
        $diffInDays = $firstDate->diffInDays($secondDate);

        $month = now()->format('m');
        $year = now()->format('Y');
        //кол во дней в месяце
        $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));

        $sum = (collect($this->revenue($dateFrom, $dateTo, $lk_id)->original)['revenue'] / $diffInDays) * $daysInMonth;
        return Response()->json([
            "revenueForecast" => round($sum)
        ]);
    }

    //WB перечислит
    public function willList(string $dateFrom, string $dateTo, $lk_id): JsonResponse
    {
        $revenue = collect($this->revenue($dateFrom, $dateTo, $lk_id)->original)['revenue'];
        $commission = collect($this->commission($dateFrom, $dateTo, $lk_id)->original)['commission'];

        $firstDate = Carbon::parse($dateFrom);
        $secondDate = Carbon::parse($dateTo);
        //Разница в дате
        $diffInDays = $firstDate->diffInDays($secondDate);

        $month = now()->format('m');
        $year = now()->format('Y');
        //кол во дней в месяце
        $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));

        $result = [
            "willList" => round(($revenue - $commission / $diffInDays) * $daysInMonth)
        ];
        return Response()->json($result);
    }

    //Реклама
    public function adv($dateFrom, $dateTo, $lk_id)
    {
        return 0;
    }

    public function ransomPercentage($dateFrom, $dateTo, $lk_id): JsonResponse
    {
        $sales = DB::table($this->WbSalesTable)
            ->select(DB::raw('count(*) as sales_count'))
            ->where('lk_id', $lk_id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->first();

        $orders = DB::table($this->WbOrdersTable)
            ->select(DB::raw('count(*) as orders_count'))
            ->where('lk_id', $lk_id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->first();

        if($sales->sales_count != 0 || $orders->orders_count != 0) {
            $ratio = $sales->sales_count / $orders->orders_count;
        }else{
            return Response()->json([
                "ransomPercentage" => null
            ]);
        }

        return Response()->json([
            "ransomPercentage" => round($ratio)
        ]);
    }

    //Чистая прибыль
    public function netProfit($dateFrom): JsonResponse
    {
        $values = Sheets::spreadsheet(env('SPREADSHEET_ID'))->sheet('ᐉ ОПИУ')->all();

        $mounts = [
            'Январь',
            'Февраль',
            'Март',
            'Апрель',
            'Май',
            'Июнь',
            'Июль',
            'Август',
            'Сентябрь',
            'Октябрь',
            'Ноябрь',
            'Декабрь'
        ];

        $parseDate = Carbon::parse($dateFrom);
        $col_position = array_search($mounts[$parseDate->format('n')-1].' '.$parseDate->format('Y'), $values[1]);

        $result = $values[103][$col_position];

        if($result == 'Чистая прибыль '){
            $result = null;
        }

        return Response()->json([
            "netProfit" => $result
        ]);
    }

    //Финансы. Маржа
    public function financeMargin(): JsonResponse
    {
        $value = Sheets::spreadsheet(env('SPREADSHEET_ID'))->sheet('Упр. баланс')->range('D3')->get();
        return Response()->json([
            'financeMargin' => $value[0][0]
        ]);
    }

    //В закупе
    public function inPurchase($lk_id)
    {
        $query = DB::table((new WbStock())->getTable())->where('lk_id', $lk_id)->sum('Price');
        return Response()->json([
            'inPurchase' => $query
        ]);
    }

    //sales
    public function sales($dateFrom, $dateTo, $lk_id)
    {
        $query = DB::table($this->WbSalesTable)->where('lk_id', $lk_id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->count();

        return Response()->json([
            'sales' => $query
        ]);
    }

    //orders
    public function orders($dateFrom, $dateTo, $lk_id): JsonResponse
    {
        $query = DB::table($this->WbOrdersTable)->where('lk_id', $lk_id)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->count();

        return Response()->json([
            'orders' => $query
        ]);
    }

    //остатки
    public function leftovers($lk_id)
    {
        $data = (new PrimaryDataService())->leftovers(Auth()->User(), $lk_id);
        return $data;
    }

    //отмены
    public function cancels(SumRequest $request): JsonResponse
    {
        $query = WbOrder::where('lk_id', $request->lk_id)
            ->whereBetween('date', [$request->dateFrom, $request->dateTo])
            ->where('isCancel', true)
            ->count();
        return Response()->json([
            "cancels" => $query
        ]);
    }

    //Возвраты
    public function returns(SumRequest $request): JsonResponse
    {
       $query = WbSale::where('lk_id', $request->lk_id)
           ->whereBetween('date', [$request->dateFrom, $request->dateTo])
           ->where('saleID', 'LIKE', 'R%')
           ->count();

       return Response()->json([
           "returns" => $query
       ]);
    }

    //Оборот
    public function turnover(SumRequest $request): JsonResponse
    {
        $dateFrom = Carbon::parse($request->dateFrom);
        $dateTo = Carbon::parse($request->dateTo);
        $diffInDays = $dateFrom->diffInDays($dateTo);

        $leftovers = (int)$this->leftovers($request->lk_id)->original['amount'][0]->total;
        $sales = (int)$this->sales($request->dateFrom, $request->dateTo, $request->lk_id)->original['sales'];
        return Response()->json([
            "turnover" => round(($leftovers * $diffInDays) / $sales)
        ]);
    }

    //Цена минус СПП
    public function retailMinusSpp(SumRequest $request)
    {
        $retail = (float)$this->getFieldSumFromPeriodTable("retail_price_withdisc_rub", $request->dateFrom, $request->dateTo, $request->lk_id)->original['total'];
        $spp = (float)$this->spp($request->dateFrom, $request->dateTo, $request->lk_id)->original['spp'];

        return Response()->json([
            "total" => round($retail - $spp)
        ]);
    }

    //Средний чек
    public function avgCheck(SumRequest $request)
    {
        //Выручка на кол-во продаж
        $sales = $this->sales($request->dateFrom, $request->dateTo, $request->lk_id)->original['sales'];
        $revenue = $this->revenue($request->dateFrom, $request->dateTo, $request->lk_id)->original['revenue'];

        if($sales == 0 || $revenue == 0){
            return Response()->json([
                "avgCheck" => null
            ]);
        }

        return Response()->json([
            "avgCheck" => round($revenue / $sales)
        ]);
    }
}
