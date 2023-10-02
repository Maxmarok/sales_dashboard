<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SumController\SumRequest;
use App\Services\WB\Calculations;
use App\Services\WB\WBSales;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function movements(SumRequest $request)
    {
        return (new WBSales())->movements($request->lk_id, $request->year);
    }

    public function cashflow()
    {
        return (new WBSales())->cashflow();
    }

    public function dashboard()
    {
        return (new WBSales())->dashboard();
    }

    public function calendar()
    {
        return (new WBSales())->calendar();
    }
}
