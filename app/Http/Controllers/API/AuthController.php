<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthController\LoginRequest;
use App\Http\Requests\AuthController\RegisterRequest;
use App\Services\AuthController\RegisterService;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return (new RegisterService())->register($request->validated());
    }

    public function login(LoginRequest $request)
    {
        return (new RegisterService())->login($request->validated());
    }
}
