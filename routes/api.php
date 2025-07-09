<?php

use App\Http\Api\AssetApi;
use App\Http\Api\InvestorApi;
use App\Http\Api\MasterApi;
use App\Http\Api\UsersApi;
use App\Http\Api\InvestmentApi;
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

Route::prefix('master')->group(function () {
    Route::get('/investortype', [MasterApi::class, 'investorType']);
    Route::get('/provinces', [MasterApi::class, 'provinces']);
    Route::get('/regencies/{province_id}', [MasterApi::class, 'regencies']);
    Route::get('/districts/{regency_id}', [MasterApi::class, 'districts']);
    Route::get('/villages/{district_id}', [MasterApi::class, 'villages']);
});

Route::prefix('asset')->group(function () {
    Route::get('/list', [AssetApi::class, 'index']);
    Route::post('/detail', [AssetApi::class, 'detail']);
});

Route::prefix('investor')->group(function () {
    Route::get('/list', [InvestorApi::class, 'list']);
    Route::get('/detail', [InvestorApi::class, 'detail']);
    Route::post('/register', [InvestorApi::class, 'register']);
    Route::get('/approve-registration', [InvestorApi::class, 'approve_registration']);
    
    Route::get('/list-investment', [InvestorApi::class, 'listInvestment']);
    Route::post('/add-investment', [InvestorApi::class, 'addInvestment']);
    Route::post('/add-documents', [InvestorApi::class, 'addDocuments']);
    Route::get('/approve-investment', [InvestorApi::class, 'approveInvestment']);
    
});

Route::prefix('/users')->group(function () {
    Route::post('/register', [UsersApi::class, 'register']);
    Route::post('/login', [UsersApi::class, 'login']);
    Route::post('/info', [UsersApi::class, 'info']);
});

Route::prefix('investment')->middleware('auth:api')->group(function () {
    // Rute ini sekarang dilindungi oleh middleware 'auth:api'
    Route::post('/submit', [InvestmentApi::class, 'submit']);
    Route::get('/list', [InvestmentApi::class, 'list']);
    Route::get('/detail/{id}', [InvestmentApi::class, 'detail']);
});

// DEFINISIKAN RUTE AKTIVASI DI SINI, DI LUAR GRUP ATAU DI DALAM JIKA PERLU
// Ini adalah cara yang benar untuk mendefinisikan rute aktivasi
Route::get('/users/activate/{id}/{hash}', [UsersApi::class, 'activate'])
    ->name('users.activate')  // <-- FIX #1: Memberi nama pada rute
    ->middleware('signed');   // <-- PENTING: Untuk keamanan URL

// Protected routes, menggunakan middleware auth:api (karena kita pakai Passport)
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('investor');
    });
    Route::prefix('/users')->group(function () {
        Route::post('/info', [UsersApi::class, 'info']);
    });
    Route::post('/users/logout', 'UsersApi@logout');
});
