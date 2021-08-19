<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/register', [AuthController::class, 'register']);   // route of authentication User whit controller AuthController.

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/clients/list', [ClientController::class, 'getAllClients']);

Route::middleware('auth:sanctum')->get('/clients/client', [ClientController::class, 'getClient']);

Route::middleware('auth:sanctum')->post('/clients/addClient', [ClientController::class, 'newAndUpdateClient']);

Route::middleware('auth:sanctum')->post('/clients/updateClient', [ClientController::class, 'updateClient']);




