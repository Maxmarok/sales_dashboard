<?php
namespace App\Services\WB;

use App\Http\Requests\ChartController\DynamicsRubRequest;
use App\Models\Lk;
use App\Models\User;
use App\Models\WbOrder;
use App\Models\WbPrice;
use App\Models\WbProduct;
use App\Models\WbReportDetailByPeriod;
use App\Models\WbSale;
use App\Models\WbStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Cookie\CookieJar;
use Carbon\CarbonPeriod;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PrimaryDataService extends WBMain {

    public function loadDetailReport(string $id, $response): void
    {
        $chunkSize = 1000; // Размер чанка
        $totalRecords = count($response);
        $totalChunks = ceil($totalRecords / $chunkSize);

        for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
            $records = [];

            $start = $chunk * $chunkSize;
            $end = min(($start + $chunkSize), $totalRecords);

            for ($i = $start; $i < $end; $i++) {
                $row = $response[$i];

                if($row) {
                    $record = [
                        "realizationreport_id" => $row['realizationreport_id'],
                        "dateFrom" => Carbon::parse($row['date_from'])->format('Y-m-d H:m:s'),
                        "dateTo" => Carbon::parse($row['date_to'])->format('Y-m-d H:m:s'),
                        "nm_id" => $row['nm_id'],
                        "subject_name" => $row['subject_name'],
                        "brand_name" => $row['brand_name'],
                        "sa_name" => $row['sa_name'],
                        "ts_name" => $row['ts_name'],
                        "doc_type_name" => $row['doc_type_name'],
                        "barcode" => $row['barcode'],
                        "quantity" => $row['quantity'],
                        "retail_price" => $row['retail_price'],
                        "retail_amount" => $row['retail_amount'],
                        "sale_percent" => $row['sale_percent'],
                        "commission_percent" => $row['commission_percent'],
                        "order_dt" => Carbon::parse($row['order_dt'])->format('Y-m-d H:m:s'),
                        "supplier_oper_name" => $row['supplier_oper_name'],
                        "sale_dt" => Carbon::parse($row['sale_dt'])->format('Y-m-d H:m:s'),
                        "retail_price_withdisc_rub" => $row['retail_price_withdisc_rub'],
                        "delivery_amount" => $row['delivery_amount'],
                        "delivery_rub" => $row['delivery_rub'],
                        "rid" => $row['rid'],
                        "ppvz_for_pay" => $row['ppvz_for_pay'],
                        "ppvz_vw_nds" => $row['ppvz_vw_nds'],
                        "ppvz_sales_commission" => $row['ppvz_sales_commission'] ?? null,
                        "additional_payment" => $row['additional_payment'],
                        "penalty" => $row['penalty'],
                        "lk" => $row['lk'] ?? null,
                        "rr_dt" => Carbon::parse($row['rr_dt'])->format('Y-m-d H:m:s'),
                        "ppvz_spp_prc" => $row['ppvz_spp_prc'] ?? null,
                        "office_name" => $row['office_name'],
                        "bonus_type_name" => $row['bonus_type_name'] ?? null,
                        "rrd_id" => $row['rrd_id'],
                        "lk_id" => $id,
                        "dateUpdate" => now()->format('Y-m-d'),
                        "return_amount" => $row['return_amount']
                    ];

                    $records[] = $record;
                }
            }
            WbReportDetailByPeriod::insert($records);
        }
    }
    public function loadDetailReports(User $user): bool
    {
        if($user->lk == null){
            return false;
        }

        foreach ($user->lk as $lk) {
            $report = WbReportDetailByPeriod::where('lk_id', $lk->id);
            $dateFrom = $report->exists() ? now()->subDay()->format('Y-m-d') : now()->subYear()->format('Y-m-d');
            $dateTo = now()->format('Y-m-d');

            $client = new Client();

            $request = $client->request("GET", "https://statistics-api.wildberries.ru/api/v1/supplier/reportDetailByPeriod?dateFrom=$dateFrom&dateTo=$dateTo", [
                "headers" => ["Authorization" => $user->getApiKey('WB', 'statistic', $lk->id)]
            ]);

            $response = json_decode($request->getBody(), true);


            // $check = $this->checkRequest($req, 'detail', ['user' => $user->id, 'Error' => $req->getBody()]);
            // if(!$check){
            //     return false;
            // }

            if($response == null){
                Log::channel('updates')->info("Данные не были получены", ["user" => $user->id]);
                return true;
            }


            if (!WbReportDetailByPeriod::where('lk_id', $lk->id)->where('rid', last($response)['rid'])->exists()) {
                $this->loadDetailReport($lk->id, $response);
            } else {
                Log::channel('reports')->info('Детальный отчет совпадает с предыдущим', ["user" => $user->id]);
            }
        }
        Log::channel('updates')->info('Детальный ответ загружен', ["user" => $user->id]);
        return true;
    }

    public function loadData(string $type, string $id, $response): void
    {
        switch($type) {
            case 'sales':
            case 'statistic':
                $this->loadSale($id, $response);
                break;
            
            case 'detail':
                $this->loadDetailReport($id, $response);
                break;

            default:
                break;
        }
    }

    public function loadSale(string $id, $response): void
    {
        $chunkSize = 1000; // Размер чанка
        $totalRecords = count($response);
        $totalChunks = ceil($totalRecords / $chunkSize);

        for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
            $records = [];

            $start = $chunk * $chunkSize;
            $end = min(($start + $chunkSize), $totalRecords);

            for ($i = $start; $i < $end; $i++) {
                $row = $response[$i];
                //record
                if($row) {
                    $record = [
                        "date" => $row['date'],
                        "lastChangeDate" => $row['lastChangeDate'],
                        "supplierArticle" => $row['supplierArticle'],
                        "techSize" => $row['techSize'],
                        "barcode" => $row['barcode'],
                        "totalPrice" => $row['totalPrice'],
                        "discountPercent" => $row['discountPercent'],
                        "isSupply" => $row['isSupply'],
                        "isRealization" => $row['isRealization'],
                        "promoCodeDiscount" => $row['promoCodeDiscount'],
                        "warehouseName" => $row['warehouseName'],
                        "countryName" => $row['countryName'],
                        "oblastOkrugName" => $row['oblastOkrugName'],
                        "regionName" => $row['regionName'],
                        "incomeID" => $row['incomeID'],
                        "saleID" => $row['saleID'],
                        "odid" => $row['odid'],
                        "spp" => $row['spp'],
                        "forPay" => $row['forPay'],
                        "finishedPrice" => $row['finishedPrice'],
                        "priceWithDisc" => $row['priceWithDisc'],
                        "nmId" => $row['nmId'],
                        "subject" => $row['subject'],
                        "category" => $row['category'],
                        "brand" => $row['brand'],
                        "IsStorno" => $row['IsStorno'],
                        "gNumber" => $row['gNumber'],
                        "sticker" => $row['sticker'],
                        "srid" => $row['srid'],
                        "dateUpdate" => now(),
                        "lk_id" => $id
                    ];

                    $records[] = $record;
                }
            }
            WbSale::insert($records);
        }
    }

    public function loadSales(User $user): bool
    {
        if($user->lk == null){
            return false;
        }

        foreach ($user->lk as $lk) {
            $sales = WbSale::where('lk_id', $lk->id);
            //$dateFrom = $sales->exists() ? now()->subDay()->format('Y-m-d') : now()->subDays(3)->format('Y-m-d');
            $dateFrom = $sales->exists() ? now()->subDay()->format('Y-m-d') : now()->subYear()->format('Y-m-d');

            $req = Http::withHeaders([
                'Authorization' => $user->getApiKey('WB', 'statistic', $lk->id),
            ])->timeout(60000)->get("https://statistics-api.wildberries.ru/api/v1/supplier/sales?dateFrom=$dateFrom");

            $this->checkRequest($req, 'sales', ['user' => $user->id, 'Error' => $req]);

            $response = $req->json();

            if($response == null){
                return true;
            }


            if (!WbSale::where('lk_id', $lk->id)->where('srid', last($response)['srid'])->exists()) {
                $this->loadSale($lk->id, $response);
            } else {
                Log::channel('reports')->info('Отчет продаж совпадает с предыдущим', ["user" => $user->id]);
            }
            
            $user->changeReportStatus('WB', 'sales', 'success');
        }
        return true;

       
    }

    public function loadOrders(User $user): bool
    {
        if($user->lk == null){
            return false;
        }

        foreach ($user->lk as $lk) {
            $orders = WbOrder::where('lk_id', $lk->id);
            $dateFrom = $orders->exists() ? now()->subDay()->format('Y-m-d') : now()->subDays(3)->format('Y-m-d');

            $req = Http::withHeaders([
                'Authorization' => $user->getApiKey('WB', 'statistic', $lk->id),
            ])->timeout(60000)->get("https://statistics-api.wildberries.ru/api/v1/supplier/orders?dateFrom=$dateFrom");

            $this->checkRequest($req, 'orders', ['user' => $user->id, 'Error' => $req]);

            $response = $req->json();

            if($response == null){
                return true;
            }

            if (!WbOrder::where('lk_id', $lk->id)->where('odid', last($response)['odid'])->exists()) {
                $chunkSize = 1000; // Размер чанка
                $totalRecords = count($response);
                $totalChunks = ceil($totalRecords / $chunkSize);

                for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
                    $records = [];

                    $start = $chunk * $chunkSize;
                    $end = min(($start + $chunkSize), $totalRecords);

                    for ($i = $start; $i < $end; $i++) {
                        $row = $response[$i];

                        $record = [
                            'date' => $row['date'],
                            'lastChangeDate' => $row['lastChangeDate'],
                            'supplierArticle' => $row['supplierArticle'],
                            'techSize' => $row['techSize'],
                            'barcode' => $row['barcode'],
                            'totalPrice' => $row['totalPrice'],
                            'discountPercent' => $row['discountPercent'],
                            'warehouseName' => $row['warehouseName'],
                            'oblast' => $row['oblast'],
                            'incomeID' => $row['incomeID'],
                            'odid' => $row['odid'],
                            'nmId' => $row['nmId'],
                            'subject' => $row['subject'],
                            'category' => $row['category'],
                            'brand' => $row['brand'],
                            'isCancel' => $row['isCancel'],
                            'cancel_dt' => $row['cancel_dt'],
                            'gNumber' => $row['gNumber'],
                            'sticker' => $row['sticker'],
                            'lk_id' => $lk['id']
                        ];
                        $records[] = $record;
                    }
                    WbOrder::insert($records);
                }
            } else {
                Log::channel('reports')->info('Отчет по заказам совпадает с предыдущим', ["user" => $user->id]);
            }

            $user->changeReportStatus('WB', 'orders', 'success');
        }
        return true;
    }

    public function loadStocks(User $user): bool
    {
        if($user->lk == null){
            return false;
        }

        foreach ($user->lk as $lk) {
            $stocks = WbStock::where('lk_id', $lk->id);
            $dateFrom = $stocks->exists() ? now()->subDay()->format('Y-m-d') : now()->subDays(3)->format('Y-m-d');

            $req = Http::withHeaders([
                'Authorization' => $user->getApiKey('WB', 'statistic', $lk->id),
            ])->timeout(60000)->get("https://statistics-api.wildberries.ru/api/v1/supplier/stocks?dateFrom=$dateFrom");

            $this->checkRequest($req, 'stocks', ['user' => $user->id, 'Error' => $req]);

            $response = $req->json();

            if($response == null){
                return true;
            }

            if (!WbStock::where('lk_id', $lk->id)->where('barcode', last($response)['barcode'])->exists()) {
                WbStock::where('lk_id', $lk->id)->delete(); //удаляем прошлые остатки
                $chunkSize = 1000; // Размер чанка
                $totalRecords = count($response);
                $totalChunks = ceil($totalRecords / $chunkSize);

                for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
                    $records = [];

                    $start = $chunk * $chunkSize;
                    $end = min(($start + $chunkSize), $totalRecords);

                    for ($i = $start; $i < $end; $i++) {
                        $row = $response[$i];

                            $record = [
                                'lastChangeDate' => $row['lastChangeDate'],
                                'supplierArticle' => $row['supplierArticle'],
                                'techSize' => $row['techSize'],
                                'barcode' => $row['barcode'],
                                'quantity' => $row['quantity'],
                                'isSupply' => $row['isSupply'],
                                'isRealization' => $row['isRealization'],
                                'quantityFull' => $row['quantityFull'],
                                'warehouseName' => $row['warehouseName'],
                                'nmId' => $row['nmId'],
                                'subject' => $row['subject'],
                                'category' => $row['category'],
                                'daysOnSite' => $row['daysOnSite'],
                                'brand' => $row['brand'],
                                'SCCode' => $row['SCCode'],
                                'Price' => $row['Price'],
                                'Discount' => $row['Discount'],
                                'dateUpdate' => now(),
                                'lk_id' => $lk->id
                            ];
                            $records[] = $record;
                        }
                        WbStock::insert($records);
                    }
            } else {
                Log::channel('reports')->info('Отчет по складам совпадает с предыдущим', ["user" => $user->id]);
            }
            $user->changeReportStatus('WB', 'stocks', 'success');
        }
        return true;
    }

    public function loadProducts(User $user): bool
    {
        if($user->lk == null){
            return false;
        }

        $body = [
            'sort' => [
                'cursor' => [
                    'limit' => 1000
                ],
                'filter' => [
                    'withPhoto' => -1
                ]
            ]
        ];

        foreach ($user->lk as $lk) {
            $req = Http::withHeaders([
                'Authorization' => $user->getApiKey('WB', 'standard', $lk->id),
            ])
            ->withBody(json_encode($body), 'application/json')
            ->timeout(60000)->post("https://suppliers-api.wildberries.ru/content/v1/cards/cursor/list");

            $this->checkRequest($req, 'stocks', ['user' => $user->id, 'Error' => $req]);

            $response = $req->json();

            if ($response == null) {
                return true;
            }

            if (!WbProduct::where('lk_id', $lk->id)->where('nmID', last($response['data']['cards'])['nmID'])->exists()) {
                ////
                $chunkSize = 1000; // Размер чанка
                $totalRecords = count($response['data']['cards']);
                $totalChunks = ceil($totalRecords / $chunkSize);

                for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
                    $records = [];

                    $start = $chunk * $chunkSize;
                    $end = min(($start + $chunkSize), $totalRecords);

                    for ($i = $start; $i < $end; $i++) {
                        $row = $response['data']['cards'][$i];

                        $record = [
                            'sizes' => json_encode($row['sizes']),
                            'mediaFiles' => json_encode($row['mediaFiles']),
                            'colors' => json_encode($row['colors']),
                            'updateAt' => $row['updateAt'],
                            'vendorCode' => $row['vendorCode'],
                            'brand' => $row['brand'],
                            'object' => $row['object'],
                            'nmID' => $row['nmID'],
                            'imtID' => $row['imtID'],
                            'isProhibited' => $row['isProhibited'],
                            'tags' => json_encode($row['tags']),
                            'lk_id' => $lk->id
                        ];
                        $records[] = $record;
                    }
                    WbProduct::insert($records);
                }
                ////
            }else {
                Log::channel('reports')->info('Товары совпадают с предыдущими', ["user" => $user->id]);
            }
        }

        return true;
    }

    //тестирую рекламу
    public function qqq()
    {
        $url = "https://cmp.wildberries.ru/backend/api/v3/fullstat/6304117";

        $cookieJar = CookieJar::fromArray([
            "_wbauid" => "10104089051662107603",
            "__wbl" => "cityId=0&regionId=0&city=%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0&phone=84957755505&latitude=55,7558&longitude=37,6176&src=1",
            "__store" => "117673_122258_122259_125238_125239_125240_507_3158_117501_120602_120762_6158_121709_124731_130744_159402_2737_117986_1733_686_132043_161812_1193_206968_206348_205228_172430_117442_117866",
            "__region" => "80_64_38_4_115_83_33_68_70_69_30_86_75_40_1_66_48_110_22_31_71_114_111",
            "__dst" => "-1029256_-102269_-2162196_-1257786",
            "x-supplier-id-external" => '59350108',
            "WBToken" => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6IjdiNzMwOTEwLWMzMzItNDgwZC04N2RiLTVkOTQxZTI3NmE2YiJ9.yE--e0j7Eh3fXP35QlHYmyL8R9krQFIY_B6TnRnX5zI',//access_token,
            "__tm" => '1680195867'
        ], 'https://cmp.wildberries.ru/');

        $client = new \GuzzleHttp\Client();
        $request = $client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-control' => 'no-store',
                'Connection' => 'keep-alive',
                'Content-Type' => 'application/json',
                'Host' => 'cmp.wildberries.ru',
                'Referer' => 'https://cmp.wildberries.ru/statistics/',
                'Sec-Fetch-Dest' => 'empty',
                'Sec-Fetch-Mode' => 'cors',
                'Sec-Fetch-Site' => 'same-origin',
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36',
                'X-User-Id' => '56794977',
                'sec-ch-ua' => '"Google Chrome";v="111", "Not(A:Brand";v="8", "Chromium";v="111"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"Windows"'
            ],
            'cookies' => $cookieJar
        ]);
        return $request->getBody();
    }

    //Остатки
    public function leftovers(User $user, $lk_id)
    {
        $query = DB::table('wb_stocks')
            ->where('lk_id', $lk_id)
            ->selectRaw('sum("wb_stocks"."quantityFull") as total')
            ->get();

        return Response()->json([
            'amount' => $query
        ]);
    }

    //График СКЛАДЫ распределение остатков по складам
    public function warehouseChart(String $lk_id)
    {
        $total_quantity = DB::table('wb_stocks')->sum('quantityFull');
        $query = DB::table('wb_stocks')
            ->where('lk_id', $lk_id)
            ->selectRaw('sum("wb_stocks"."quantityFull") as total, "wb_stocks"."warehouseName", ROUND(sum("wb_stocks"."quantityFull") / '.$total_quantity.' * 100, 1) as percentage')
            ->groupBy('wb_stocks.warehouseName')
            ->get();

        return Response()->json([
            "warehouses" => $query
        ]);
    }

    //График СКЛАДЫ распределение продаж по складам
    public function warehouseSales($lk_id)
    {
        $results = DB::table('wb_sales')
            ->where('lk_id', $lk_id)
            ->select('warehouseName', DB::raw('COUNT(*) as sales_count'),
                DB::raw('ROUND(100 * COUNT(*) / SUM(COUNT(*)) OVER ()) as sales_percentage'))
            ->groupBy('warehouseName')
            ->get();

        return Response()->json([
            'warehouses' => $results
        ]);
    }

    //График динамика развития
    public function developmentDynamics(DynamicsRubRequest $request)
    {
        $format = '';
        switch ($request->get('format')){
            case 'y':
                $format = 'Y';
                break;
            case 'w':
                $format = 'W';
                break;
            default:
                $format = $request->get('format');
                break;
        }

        $orders = WbOrder::orderBy('date')
        ->whereBetween('date', [$request->dateFrom, $request->dateTo])
        ->where('lk_id', $request->lk_id)
        ->get()
        ->groupBy(function($orders) use ($format){
            return Carbon::parse($orders->date)->format($format);
        })->map(function($orders) use ($request){
            if($request->type == 'sum'){
                return $orders->sum('totalPrice');
            }else{
                return $orders->count();
            }
        });

        $sales = WbSale::orderBy('date')
            ->whereBetween('date', [$request->dateFrom, $request->dateTo])
            ->where('lk_id', $request->lk_id)
            ->get()
            ->groupBy(function($sales) use ($format){
                return Carbon::parse($sales->date)->format($format);
            })->map(function($sales) use ($request){
                if($request->type == 'sum'){
                    return $sales->sum('totalPrice');
                }else {
                    return $sales->count();
                }
            });

        $dates = CarbonPeriod::create($request->dateFrom, $request->dateTo);
        foreach ($dates as $date) {
            $formattedDate = $date->format($format);
            if (!isset($orders[$formattedDate])) {
                $orders[$formattedDate] = 0;
            }
            if (!isset($sales[$formattedDate])) {
                $sales[$formattedDate] = 0;
            }
        }

        return Response()->json([
            "orders" => $orders,
            "sales" => $sales
        ]);
    }

    public function loadPrices(User $user)
    {
        if($user->lk == null){
            return false;
        }

        foreach ($user->lk as $lk) {
            $req = Http::withHeaders([
                'Authorization' => $user->getApiKey('WB', 'standard', $lk->id),
            ])->timeout(60000)->get("https://suppliers-api.wildberries.ru/public/api/v1/info");

            $this->checkRequest($req, 'prices', ['user' => $user->id, 'Error' => $req]);

            $response = $req->json();

            if($response == null){
                return true;
            }

            if (!WbPrice::where('lk_id', $lk->id)->where('nmId', last($response)['nmId'])->exists()) {
                WbPrice::where('lk_id', $lk->id)->delete(); //удаляем прошлые цены
                $chunkSize = 1000; // Размер чанка
                $totalRecords = count($response);
                $totalChunks = ceil($totalRecords / $chunkSize);

                for ($chunk = 0; $chunk < $totalChunks; $chunk++) {
                    $records = [];

                    $start = $chunk * $chunkSize;
                    $end = min(($start + $chunkSize), $totalRecords);

                    for ($i = $start; $i < $end; $i++) {
                        $row = $response[$i];

                        $record = [
                            'nmId' => $row['nmId'],
                            'price' => $row['price'],
                            'discount' => $row['discount'],
                            'promoCode' => $row['promoCode'],
                            'lk_id' => $lk->id
                        ];
                        $records[] = $record;
                    }
                    WbPrice::insert($records);
                }
            } else {
                Log::channel('reports')->info('Цены остались прежними', ["user" => $user->id]);
            }
            $user->changeReportStatus('WB', 'prices', 'success');
        }
        return true;
    }
}
