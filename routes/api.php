<?php

use App\Http\Api\AssetApi;
use App\Http\Api\MasterApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('asset')->group(function () {
    Route::get('/list', [AssetApi::class, 'index']);
    Route::post('/detail', [AssetApi::class, 'detail']);
});

Route::prefix('master')->group(function () {
    Route::get('/investortype', [MasterApi::class, 'investorType']);
    Route::get('/provinces', [MasterApi::class, 'provinces']);
    Route::get('/regencies/{province_id}', [MasterApi::class, 'regencies']);
    Route::get('/districts/{regency_id}', [MasterApi::class, 'districts']);
    Route::get('/villages/{district_id}', [MasterApi::class, 'villages']);
});
