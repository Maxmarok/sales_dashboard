<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => '/v1',
    'as' => 'api.',
], function(){
    Route::post('register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\API\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (){
        Route::get('me', [App\Http\Controllers\API\ProfileController::class, 'userInfo']);
        Route::post('load-primary-data', [App\Http\Controllers\API\DataController::class, 'loadPrimaryData']);
        Route::post('update-data', [App\Http\Controllers\API\DataController::class, 'updateData']);
        Route::get('notifications', [App\Http\Controllers\API\NotificationController::class, 'index']);
        Route::get('notification/{id}', [App\Http\Controllers\API\NotificationController::class, 'view']);
    
        /*
         * Profile
         */
        Route::prefix('profile')->group(function (){
            Route::patch('update', [App\Http\Controllers\API\ProfileController::class, 'update']);
            #TODO добавить permission на API KEY
            Route::post('add-api-key', [App\Http\Controllers\API\ProfileController::class, 'addApiKey']);
            Route::post('delete-api-key', [App\Http\Controllers\API\ProfileController::class, 'deleteApiKey']);
            Route::get('api-key/list', [App\Http\Controllers\API\ProfileController::class, 'apiKeyList']);
            //API KEYS list api keys for user
            //MarketPlaces list mp for user
            Route::get('update-time/{lk_id}', [App\Http\Controllers\API\UpdateController::class, 'updateTime']);
    
            //Lk
            Route::prefix('lk')->group(function(){
                Route::get('/index/{id}', [App\Http\Controllers\API\ProfileController::class, 'index']);
                Route::post('/add', [App\Http\Controllers\API\ProfileController::class, 'addLk']);
                Route::get('/list', [App\Http\Controllers\API\ProfileController::class, 'listLk']);
                Route::post('/delete', [App\Http\Controllers\API\ProfileController::class, 'lkDelete']);
                Route::post('/update/{id}', [App\Http\Controllers\API\ProfileController::class, 'lkUpdate']);
            });
        });
    });
});

