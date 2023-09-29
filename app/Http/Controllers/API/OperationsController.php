<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OperationsController\AddOperationRequest;
use App\Http\Requests\OperationsController\AddAccountRequest;
use App\Services\OperationsController\OperationsService;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function accounts()
    {
        return (new OperationsService())->accounts();
    }

    public function add_account(AddAccountRequest $request)
    {
        return (new OperationsService())->add_account($request->validated());
    }

    public function operations()
    {
        return (new OperationsService())->operations();
    }

    public function add_operation(AddOperationRequest $request)
    {
        return (new OperationsService())->add_operation($request->validated());
    }
}
