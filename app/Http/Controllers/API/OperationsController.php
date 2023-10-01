<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OperationsController\AddOperationRequest;
use App\Http\Requests\OperationsController\AddAccountRequest;
use App\Http\Requests\OperationsController\AddArticleRequest;
use App\Http\Requests\OperationsController\UpdateAccountRequest;
use App\Http\Requests\OperationsController\UpdateArticleRequest;
use App\Http\Requests\OperationsController\UpdateOperationRequest;
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

    public function update_account(UpdateAccountRequest $request)
    {
        return (new OperationsService())->update_account($request->validated());
    }

    public function operations()
    {
        return (new OperationsService())->operations();
    }

    public function add_operation(AddOperationRequest $request)
    {
        return (new OperationsService())->add_operation($request->validated());
    }

    public function update_operation(UpdateOperationRequest $request)
    {
        return (new OperationsService())->update_operation($request->validated());
    }

    public function articles()
    {
        return (new OperationsService())->articles();
    }

    public function add_article(AddArticleRequest $request)
    {
        return (new OperationsService())->add_article($request->validated());
    }

    public function update_article(UpdateArticleRequest $request)
    {
        return (new OperationsService())->update_article($request->validated());
    }
}
